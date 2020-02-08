<?php
namespace Uzink\BackendBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target("CLASS")
 */
class NotContainsAnotherLeaders extends Constraint
{
    public $message = 'No se puede asignar esta categoría a este lider porque en las categorías dependientes existe un lider diferente.';

    /**
     * Returns whether the constraint can be put onto classes, properties or
     * both.
     *
     * This method should return one or more of the constants
     * Constraint::CLASS_CONSTRAINT and Constraint::PROPERTY_CONSTRAINT.
     *
     * @return string|array One or more constant values
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

