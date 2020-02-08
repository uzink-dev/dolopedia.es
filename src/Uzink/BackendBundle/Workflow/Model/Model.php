<?php

namespace Uzink\BackendBundle\Workflow\Model;

use Lexik\Bundle\WorkflowBundle\Model\ModelInterface;

class Model implements ModelInterface {
    private $entity;

    public function __construct($entity) {
        $this->entity = $entity;
    }

    public function getEntity() {
        return $this->entity;
    }

    public function setStatus($status)
    {
        if ($this->entity) $this->entity->setStatus($status);
    }

    public function getStatus()
    {
        if ($this->entity) return $this->entity->getStatus();
        return null;
    }

    /**
     * Returns a unique identifier.
     *
     * @return mixed
     */
    public function getWorkflowIdentifier()
    {
        return md5(get_class($this->entity).'-'.$this->entity->getId());
    }

    /**
     * Returns data to store in the ModelState.
     *
     * @return array
     */
    public function getWorkflowData()
    {
        $entityId = ($this->entity)?$this->entity->getId():null;

        return array(
            'id' => $entityId,
        );
    }

    /**
     * Returns the object of the workflow.
     *
     * @return mixed
     */
    public function getWorkflowObject()
    {
        $this->entity;
    }
}