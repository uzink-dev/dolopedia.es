<?php

namespace Uzink\FrontBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ImageExtension extends \Twig_Extension
{
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('imageHandler', array($this, 'imageHandlerFilter')),
            new \Twig_SimpleFilter('entityHandler', array($this, 'entityHandlerFilter')),
            new \Twig_SimpleFilter('categoryHandler', array($this, 'categoryHandlerFilter')),
            new \Twig_SimpleFilter('userHandler', array($this, 'userHandlerFilter')),
            new \Twig_SimpleFilter('centerHandler', array($this, 'centerHandlerFilter')),
            new \Twig_SimpleFilter('activityHandler', array($this, 'activityHandlerFilter')),
        );
    }

    public function entityHandlerFilter($entity)
    {
        switch (get_class($entity)) {
            case 'Uzink\\BackendBundle\\Entity\\User':
                return $this->userHandlerFilter($entity->getImage());
                break;

            case 'Uzink\\BackendBundle\\Entity\\Category':
                return $this->categoryHandlerFilter($entity->getImage(), 'category');
                break;

            case 'Uzink\\BackendBundle\\Entity\\Center':
            case 'Uzink\\BackendBundle\\Entity\\Promoter':
                return $this->imageHandlerFilter($entity->getImage(), 'institution');
                break;
        }

        return '';
    }

    public function imageHandlerFilter($image, $format = null, $defaultImage = null)
    {
        $url = null;

        if ($image) {
            $url = $this->container->get('liip_imagine.cache.manager')->getBrowserPath($image, $format);
        } elseif ($defaultImage) {
            $url = $this->container->get('templating.helper.assets')->getUrl($defaultImage);
        }

        return $url;
    }

    public function categoryHandlerFilter($image, $format = null) {
        return $this->imageHandlerFilter($image, $format, 'bundles/front/img/defaultImages/defaultCategory.jpg');
    }

    public function userHandlerFilter($image, $format = null) {
        if (!$format) $format = 'user_thumb_large';
        return $this->imageHandlerFilter($image, $format, 'bundles/front/img/defaultImages/defaultUser.png');
    }

    public function centerHandlerFilter($image, $format = null) {
        if (!$format) $format = 'institution';
        return $this->imageHandlerFilter($image, $format, 'bundles/front/img/defaultImages/defaultCenter.jpg');
    }

    public function getName()
    {
        return 'image_extension';
    }
}
