<?php
namespace Uzink\BackendBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Uzink\BackendBundle\Entity\Category;

class CategoryToNumberTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Category|null $category
     * @return string
     */
    public function transform($category)
    {
        if (empty($category)) {
            return '';
        }

        if (is_numeric($category)) return $category;
        else return $category->getId();
    }

    /**
     * Transforms a string (number) to an object (category).
     *
     * @param  string $id
     *
     * @return Category|null
     *
     * @throws TransformationFailedException if object (category) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return null;
        }

        $category = $this->om
            ->getRepository('BackendBundle:Category')
            ->findOneBy(array('id' => $id))
        ;

        if (null === $category) {
            throw new TransformationFailedException(sprintf(
                'An issue with number "%s" does not exist!',
                $id
            ));
        }

        return $category;
    }
}