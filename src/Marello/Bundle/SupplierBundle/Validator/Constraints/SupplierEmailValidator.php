<?php

namespace Marello\Bundle\SupplierBundle\Validator\Constraints;

use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\LogicException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class SupplierEmailValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof SupplierEmail) {
            throw new UnexpectedTypeException($constraint, SupplierEmail::class);
        }

        if (!$value instanceof Supplier) {
            throw new LogicException(sprintf(
                'The value must be instance of %s, got %s',
                Supplier::class,
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        if ($value->getPoSendBy() === Supplier::SEND_PO_BY_EMAIL && !$value->getEmail()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('email')
                ->addViolation();
        }
    }
}
