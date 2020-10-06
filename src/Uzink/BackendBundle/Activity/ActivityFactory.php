<?php
namespace Uzink\BackendBundle\Activity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Routing\Router;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Yaml\Parser;
use Uzink\BackendBundle\Entity\Activity;
use Uzink\BackendBundle\Entity\ActivityClass;
use Uzink\BackendBundle\Entity\User;

class ActivityFactory extends Container {
    //<editor-fold desc="Properties">
    const TRANSLATION_DOMAIN = 'activity';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FileLocator
     */
    private $fileLocator;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var object
     */
    protected $entity;

    /**
     * @var User
     */
    protected $sender;

    /**
     * @var Activity
     */
    protected $mainActivity;

    /**
     * @var ArrayCollection
     */
    protected $activities;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->em = $container->get('doctrine')->getManager();
        $this->translator = $container->get('translator');
        $this->fileLocator = $container->get('file_locator');
        $this->router = $container->get('router');
    }
    //</editor-fold>

    //<editor-fold desc="Activity Generation">
    /**
     * Create activities with the parameters
     *
     * @param string $type
     * @param object $entity
     * @param User|array|ArrayCollection $owners
     * @param User $sender
     */
    public function create($type, $entity, $owners, $sender)
    {
        $this->type = $type;
        $this->entity = $entity;
        $this->sender = $sender;
        $this->activities = new ArrayCollection();

        $this->cleanUsers($owners);

        foreach ($owners as $owner) {
            if ($owner == $sender or $sender == null)
                $this->generateActivityToMe($owner);
            else
                $this->generateActivityToOther($owner);
        }

        foreach ($this->activities as $activity) {
            $this->em->persist($activity);
        }

        $this->em->flush();
    }

    private function cleanUsers($owners)
    {
        $tempOwners = new ArrayCollection();

        if ($owners instanceof User) {
            $tempOwners->add($owners);
            return $tempOwners;
        }

        foreach ($owners as $owner) {
            if (!$tempOwners->contains($owner) && $owner instanceof User)
                $tempOwners->add($owner);
        }

        return $tempOwners;
    }

    private function generateActivity(User $user)
    {
        $entityMetadata = $this->getEntityMetadata();
        $activity = new Activity();

        if ($this->sender) $activity->setSender($this->sender);
        $activity->setReceiver($user);
        $activity->setType($this->type);
        $activity->setEntityClass($entityMetadata['name']);
        $activity->setEntityId($entityMetadata['id']);

        return $activity;
    }

    private function generateActivityToMe(User $user)
    {
        $activity = $this->generateActivity($user);
        $activity->setToken('me');

        $this->activities->add($activity);

        $token = $this->getToken($activity);
        $this->setFixedData($token, $activity);
    }

    private function generateActivityToOther(User $user)
    {
        $activity = $this->generateActivity($user);
        $activity->setToken('other');

        $this->activities->add($activity);
    }

    private function getEntityMetadata()
    {
        $actParameterRepo = $this->em->getRepository('BackendBundle:ActivityClass');

        $className = get_class($this->entity);
        $classId = $this->entity->getId();

        $activityClass = $actParameterRepo->findOneBy(array('classType' => $className));
        if (!$activityClass) {
            $activityClass = new ActivityClass();
            $activityClass->setClassType($className);
            $this->em->persist($activityClass);
            $this->em->flush();
        }

        return array(
            'name'  => $activityClass,
            'id'    => $classId
        );
    }

    private function setFixedData($token, $activity)
    {
        $this->mainActivity = $activity;
        $fields = $this->getFields($token);

        foreach ($fields as $field) {
            if ($fieldValue = $this->getEntityValue($field, $this->entity)) {
                $this->updateFixedValues($field, $fieldValue);
            }
        }

    }
    //</editor-fold>

    //<editor-fold desc="Activity Render">
    public function createMessage(Activity $activity)
    {
        try {
            $this->mainActivity = $activity;
            $token = $this->getToken($activity);
            $this->inflateEntity($activity);

            $fields = $this->getFields($token);
            $filledFields = array();
            foreach ($fields as $field) {
                $fieldValue = $this->getFieldValue($field);
                $performedField = $this->getPerformedField($field, $fieldValue);
                $filledFields['%'.$field.'%'] = $performedField;
            }

            return $this->translator->trans($token, $filledFields, self::TRANSLATION_DOMAIN);
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getToken(Activity $activity)
    {
        $token = $activity->getType() . '.' . $activity->getToken();

        return $token;
    }

    private function inflateEntity(Activity $activity)
    {
        $entityClass = $activity->getEntityClass();
        $entityId = $activity->getEntityId();

        $repo = $this->em->getRepository($entityClass->getClassType());
        $entity = $repo->find($entityId);

        $this->entity = $entity;
    }

    private function getFields($token)
    {
        // Matches words between '%' symbol
        $regex = "/\\%([^%]+)\\%/";
        $string = $this->translator->trans($token, array(), self::TRANSLATION_DOMAIN);
        $matches = null;

        preg_match_all($regex, $string, $matches);

        return $matches[1];
    }

    private function getFieldValue($field)
    {
        if ($fieldValue = $this->getEntityValue($field, $this->entity)) {
            $this->updateFixedValues($field, $fieldValue);
            return $fieldValue;
        }

        if ($fieldValue = $this->getEntityValue($field, $this->mainActivity))
            return $fieldValue;

        if ($fieldValue = $this->getFixedValue($field))
            return $fieldValue;

        throw new \Exception('Unable to get field value for field "'. $field .'" in activity type"' . $this->mainActivity->getType() . '", we tried on entity and activity');
    }

    private function getFixedValue($field)
    {
        $metadata = $this->mainActivity->getEntityMetadata();

        $fieldMetadataExist = array_key_exists($field, $metadata);
        if ($fieldMetadataExist) {
            return $metadata[$field];
        }

        return null;
    }

    private function updateFixedValues($field, $value)
    {
        $metadata = $this->mainActivity->getEntityMetadata();
        $value = is_object($value) ? $value->__toString() : $value;

        $fieldMetadataExist = array_key_exists($field, $metadata);
        if (!$fieldMetadataExist or ($fieldMetadataExist && $metadata[$field] != $value)) {
            $metadata[$field] = $value;

            $this->mainActivity->setEntityMetadata($metadata);
            $this->em->persist($this->mainActivity);
            $this->em->flush();
        }
    }

    public function getPerformedField($field, $fieldValue)
    {
        $fieldConfig = $this->getConfiguration($field);
        if (!$fieldConfig) return $fieldValue;

        $performedField = $fieldValue;
        $url = $this->getUrl($field, $fieldConfig);
        if ($url) $performedField = '<a href="' . $url . '">' . $fieldValue . '</a>';

        return $performedField;
    }

    private function getConfiguration($field) {
        $yaml = new Parser();
        $path = $this->container->get('file_locator')->locate('activities.config.yml', __DIR__, true);
        $config = $yaml->parse(file_get_contents($path));

        if ($this->entity)
            $className = $this->em->getClassMetadata(get_class($this->entity))->getName();
        else
            $className = 'nothing';

        // This array defines the config priority
        $configScopes = [
            'event'     => $this->mainActivity->getType(),
            'entity'    => $className,
        ];
        $configScope = null;
        $configType = null;
        $configData = null;

        $configPath = 'activity#fields#' . $field;
        if ($this->getConfig($config, $configPath)) {
            $configScope = 'activity';
            $configData = $config['activity']['fields'][$field];
        }

        foreach($configScopes as $scopeName => $scopeValue) {
            $configPath = $scopeName . '#' . $scopeValue . '#fields#' . $field;
            if ($this->getConfig($config, $configPath)) {
                $configScope = $scopeName;
                $configType = $scopeValue;
                $configData = $config[$scopeName][$scopeValue]['fields'][$field];
                break;
            }
        }

        $configuration = array();
        $configuration['scope'] = $configScope;
        $configuration['type'] = $configType;
        $configuration['data'] = $configData;

        return $configuration;
    }

    private function getConfig($config, $fieldConfig)
    {
        $tempConfig = $config;
        $configChunks = explode('#', $fieldConfig);
        foreach ($configChunks as $configChunk) {
            if (array_key_exists($configChunk, $tempConfig) && is_array($tempConfig[$configChunk]))
                $tempConfig = $tempConfig[$configChunk];
            else
                return null;
        }

        return $tempConfig;
    }

    private function getUrl($field, $fieldConfig)
    {
        try {
            $configScope = $fieldConfig['scope'];
            $routeName = $fieldConfig['data']['route']['name'];
            $routeParams = $fieldConfig['data']['route']['params'];
        } catch (\Exception $e) {
            throw new \Exception('Bad configuration for ' . $field . ' in scope ' . $fieldConfig['scope']);
        }

        $filledParams = array();

        switch ($configScope) {
            case 'activity':
                foreach($routeParams as $key => $param) {
                    $filledParams[$key] = $this->getEntityValue($param, $this->mainActivity);
                }
                break;
            case 'entity':
            case 'event':
                foreach($routeParams as $key => $param) {
                    $filledParams[$key] = $this->getEntityValue($param, $this->entity);
                }
                break;
        }

        if (!$routeName or !$routeParams) return null;

        foreach($filledParams as $param){
            if (!$param) return null;
        }

        return $this->router->generate($routeName, $filledParams);
    }
    private function getEntityValue($path, $entity)
    {
        if (!$entity) return null;

        $value = null;
        $auxEntity = $entity;
        $properties = explode('.', $path);

        foreach ($properties as $property) {
            $value = $this->getPropertyValue($property, $auxEntity);
            if (!$value) return null;
            $auxEntity = $value;
        }

        return $value;
    }

    private function getPropertyValue($property, $entity)
    {
        $rc = new \ReflectionClass(get_class($entity));
        if ($rc->hasMethod($property)) return $entity->$property();

        $arrayPrefixMethods = ['get', 'has', 'is'];
        foreach ($arrayPrefixMethods as $currentPrefix) {
            $performedMethod = $currentPrefix . ucfirst($property);
            if ($rc->hasMethod($performedMethod)) return $entity->$performedMethod();
        }

        return null;
    }
    //</editor-fold>
} 