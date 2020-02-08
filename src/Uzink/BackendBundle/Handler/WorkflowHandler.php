<?php

namespace Uzink\BackendBundle\Handler;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Entity\Draft;
use Uzink\BackendBundle\Entity\Request;

class WorkflowHandler {
    private $container;

    private $pNamespace = '\\Uzink\\BackendBundle\\Workflow\\Model\\';
    private $pClasses = array(
        'article_request'  => 'RequestModel',
        'article_creation' => 'ArticleModel',
        'article_edition'  => 'DraftModel'
    );

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createModel($entity) {
        $processName = $this->getProcessName($entity);
        $class = $this->pNamespace . $this->pClasses[$processName];
        $model = new $class($entity);

        return $model;
    }

    public function getProcessHandler($entity) {
        $processName = $this->getProcessName($entity);
        $processHandler = $this->container->get('lexik_workflow.handler.' . $processName);

        return $processHandler;
    }

    public function startAndSave($entity) {
        $this->start($entity);

        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->persist($entity);
        $em->flush();
    }

    public function start($entity) {
        $wfModel = $this->createModel($entity);
        $wfProcess = $this->getProcessHandler($entity);
        $wfProcess->start($wfModel);

        return $wfModel;
    }

    public function reachNextState($entity, $step, $wfModel = null) {
        if (!$wfModel) $wfModel = $this->createModel($entity);
        $wfProcess = $this->getProcessHandler($entity);
        $modelState = $wfProcess->reachNextState($wfModel, $step);

        if ($modelState->getSuccessful()) {
            $em = $this->container->get('doctrine.orm.entity_manager');
            $em->persist($wfModel->getEntity());
            $em->flush();

            return null;
        } else {
            return $modelState->getErrors();
        }
    }

    private function getProcessName($entity) {
        if ($entity instanceof Request) {
            switch ($entity->getType()) {
                case Request::TYPE_REQUEST_NEW :
                case Request::TYPE_REQUEST_MODIFY :
                case Request::TYPE_ARTICLE_PUBLICATION :
                case Request::TYPE_ARTICLE_VALIDATION :
                    return 'article_request';

                case Request::TYPE_EDITION_CREATION :
                case Request::TYPE_EDITION_MODIFICATION :
                    return 'article_creation';
            }
        }
        if ($entity instanceof Article) return 'article_creation';
        if ($entity instanceof Draft)   return 'article_edition';
    }
}