<?php

namespace Uzink\UtilsBundle\EventListener;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\Common\EventSubscriber;
use Uzink\UtilsBundle\Entity\Utils;


class SeoEventListener implements EventSubscriber
{
    protected $encoder;
    protected $dispatcher;
    protected $em;
    protected $uow;
    protected $eventManager;

    public function __construct(EncoderFactory $encoder, EventDispatcherInterface $dispatcher) {
        $this->encoder = $encoder;
        $this->dispatcher = $dispatcher;
    }

    public function getSubscribedEvents()
    {
        return [
            'onFlush'
        ];
    }
    
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $this->em = $eventArgs->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();
        $this->eventManager = $this->em->getEventManager();

        $this->eventManager->removeEventListener('onFlush', $this);
        
        foreach ($this->uow->getScheduledEntityInsertions() AS $entity) {
            $this->updateSlug($entity);
        }

        foreach ($this->uow->getScheduledEntityUpdates() as $entity) {
            $this->updateSlug($entity);
        }

        $this->eventManager->addEventListener('onFlush', $this);
    }
    
    protected function updateSlug($entity)
    {
        $meta = $this->em->getClassMetadata(get_class($entity));

        if($meta->getReflectionClass()->hasMethod('getSluggableFields') && $meta->hasField('seoSlug'))
        {
            $changeset = $this->uow->getEntityChangeSet($entity);

            $valores_sluggables = $entity->getSluggableFields();

            $valor_sluggable = '';

            foreach($valores_sluggables as $valor)
            {        
                if(isset($changeset[$valor]))
                {
                    $valor_sluggable = $valor;
                }
            }

            if(strlen($valor_sluggable) > 0)
            {
                $titulo_nuevo = $changeset[$valor_sluggable][1];

                if($titulo_nuevo != null)
                {
                    $slug = $this->getSlug($titulo_nuevo, $entity, $meta);
                    $meta->getReflectionProperty('seoSlug')->setValue($entity, $slug);
                }
            }
            $seoSlug = $meta->getReflectionProperty('seoSlug')->getValue($entity);
            if(empty($seoSlug)) {

                $sluggableField = null;
                foreach($valores_sluggables as $field) {
                    if($meta->hasField($field)) {
                        $sluggableField = $field;
                        break;
                    }
                }

                if($sluggableField) {
                    $sluggableValue = $meta->getReflectionProperty($sluggableField)->getValue($entity);
                    if($sluggableValue != null)
                    {
                        $slug = $this->getSlug($sluggableValue, $entity, $meta);
                        $meta->getReflectionProperty('seoSlug')->setValue($entity, $slug);
                    }
                }
            }
            
            $this->uow->recomputeSingleEntityChangeSet($meta, $entity);
        }
    }
    
    
    protected function getSlug($titulo_nuevo, $entity, $meta)
    {

        $counter = 0;

        $unique_slug = false;

        $slug_base = Utils::slugify($titulo_nuevo);

        $slug = $slug_base;

        while(!$unique_slug)
        {

            if($counter > 0)
            {
                $slug = $slug_base.'-'.$counter;
            }

            $results = $this->em->getRepository($meta->getReflectionClass()->getName())
                    ->createQueryBuilder('t')    
                    ->andWhere("t.seoSlug = '$slug'")
                    ->getQuery()
                    ->getResult();

            if(count($results) == 0)
            {
                $unique_slug = true;

            }

            $counter++;

        }
        
        return $slug;
    }
    
}