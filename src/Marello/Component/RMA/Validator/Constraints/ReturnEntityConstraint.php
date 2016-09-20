<?php
namespace Marello\Component\RMA\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ReturnEntityConstraint extends Constraint
{
    /** @var string */
    public $message = 'At least one item should be returned.';

    /**
     * @return string
     */
    public function validatedBy()
    {
        return 'Marello\Component\RMA\Validator\ReturnEntityValidator';
    }

    /**
     * @return string
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
