<?php

namespace Marello\Component\RMA\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ReturnItemConstraint extends Constraint
{
    /** @var string */
    public $message = 'Returned quantity is greater than ordered.';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'Marello\Component\RMA\Validator\ReturnItemValidator';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
