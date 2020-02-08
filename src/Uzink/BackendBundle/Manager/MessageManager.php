<?php
namespace Uzink\BackendBundle\Manager;

use InvalidArgumentException;
use Uzink\BackendBundle\Entity\Message;
use Uzink\BackendBundle\Entity\User;
use Uzink\BackendBundle\Search\MessageSearch;
use Uzink\BackendBundle\Security\Permission\PermissionMap;

class MessageManager extends Manager
{
    protected $aclManager;

    public function __construct($class, $formClass) {
        $this->class = $class;
        $this->formClass = $formClass;
    }

    public function createResponse(Message $message) {
        $responseMessage = $this->create();
        $responseMessage->setSender($message->getReceiver());
        $responseMessage->setReceiver($message->getSender());
        $responseMessage->setSubject('RE: ' . $message->getSubject());

        return $responseMessage;
    }

    public function readed(Message $message){
        $message->setReaded(true);
        $this->save($message);
        return $message;
    }

    public function getAll(User $user) {
        $messages = array();
        $messages[Message::RECEIVED] = $this->repo->findByReceiver($user);
        $messages[Message::SENT] = $this->repo->findBySender($user);

        return $messages;
    }

    public function getForm()
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        if ($this->formClass) {
            return new $this->formClass($user);
        }
    }

    /**
     * @param AclManager $aclManager
     */
    public function setAclManager(AclManager $aclManager)
    {
        $this->aclManager = $aclManager;
    }

    public function setPermissions($entity)
    {
        $aclManager = $this->aclManager;

        if (!($entity instanceof Message))
            throw new InvalidArgumentException('The entity must be a Message, given ' . get_class($entity));

        $sender = $entity->getSender();
        $receiver = $entity->getReceiver();
        $multipleReceivers = $entity->getMultipleReceivers();

        if ($sender) $aclManager->grant($entity, array(PermissionMap::PERMISSION_OWNER), $sender);
        $aclManager->grant($entity, array(PermissionMap::PERMISSION_VIEW), $receiver);
        foreach ($multipleReceivers as $receiver) {
            $aclManager->grant($entity, array(PermissionMap::PERMISSION_VIEW), $receiver);
        }
    }
}