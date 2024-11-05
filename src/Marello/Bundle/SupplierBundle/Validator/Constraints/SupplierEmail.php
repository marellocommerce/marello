<?php

namespace Marello\Bundle\SupplierBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class SupplierEmail extends Constraint
{
    public string $message = 'marello.supplier.supplier.messages.error.email';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
