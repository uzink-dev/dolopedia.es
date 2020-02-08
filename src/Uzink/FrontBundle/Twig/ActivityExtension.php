<?php

namespace Uzink\FrontBundle\Twig;

use Uzink\BackendBundle\Activity\ActivityFactory;

class ActivityExtension extends \Twig_Extension
{
    /**
     * @var ActivityFactory
     */
    private $activityFactory;

    /**
     * @var \Twig_Environment
     */
    private $twigEngine;

    public function __construct(ActivityFactory $activityFactory, \Twig_Environment $twigEngine) {
        $this->activityFactory = $activityFactory;
        $this->twigEngine = $twigEngine;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('renderActivities', array($this, 'renderActivities'), array('is_safe' => array('all'))),
        );
    }

    public function renderActivities($activities)
    {
        if (count($activities) > 0 ) {
            $html = '';
            foreach($activities as $activity) {
                $message = $this->activityFactory->createMessage($activity);
                if ($message) {
                    $image = $activity->getSender()?$activity->getSender()->getImage():'dolopedia';
                    $createdAt = $activity->getCreatedAt();

                    $html .= $this->twigEngine->render(
                        'FrontBundle:Component:partial.activity.html.twig',
                        array(
                            'message'   => $message,
                            'image'     => $image,
                            'createdAt' => $createdAt
                        )
                    );
                }
            }

            return $html;
        } else {
            return '';
        }
    }

    public function getName()
    {
        return 'activity_extension';
    }
}
