<?php
namespace Uzink\BackendBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Uzink\BackendBundle\Entity\Category;

class NotContainsAnotherLeadersValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if ($value instanceof Category) {
            if ($value->hasChildDifferentOwner()) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('owner')
                    ->addViolation();
            }
        }
    }
}