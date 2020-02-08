<?php
namespace Uzink\BackendBundle\Workflow\Listener;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\WorkflowBundle\Event\StepEvent;
use Lexik\Bundle\WorkflowBundle\Event\ValidateStepEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Uzink\BackendBundle\Entity\Article;
use Uzink\BackendBundle\Workflow\Model\ArticleModel;

class ArticleCreationProcessSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return array(
            // Steps Handling
            'article_creation.created.reached' => array(
                'handleReachedCreated',
            ),
        );
    }

    // Steps Handling
    public function handleReachedCreated(StepEvent $event)
    {
        $wfModel = $event->getModel();
        $article = new Article();
        $request = $wfModel->getEntity();
        $prevRequest = $request->getPreviousRequest();

        $article->setEditor($request->getAssignedUser());
        $article->setSupervisor($request->getUserTo());
        $article->setOwner($request->getUserFrom());
        $article->setTitle($request->getTitle());
        $article->setCategory($request->getCategory());
        $article->addRequest($request);
        $article->addCollaborators($request->getUserFrom());
        $article->addCollaborators($prevRequest->getUserFrom());
        $request->setArticle($article);

        $this->em->persist($article);
        $this->em->persist($request);
        $this->em->flush();
    }
}

