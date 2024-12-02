<?php

namespace Marello\Bundle\CustomerBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueCustomerEmail extends Constraint
{
    public $message = 'marello.customer.email.message';

    public function validatedBy()
    {
        return 'marello_customer.unique_customer_email_validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
