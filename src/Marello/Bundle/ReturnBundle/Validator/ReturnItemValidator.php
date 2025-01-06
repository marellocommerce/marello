<?php

namespace Marello\Bundle\ReturnBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Marello\Bundle\ReturnBundle\Entity\ReturnItem;
use Marello\Bundle\ReturnBundle\Util\ReturnHelper;

class ReturnItemValidator extends ConstraintValidator
{
    public function __construct(protected ReturnHelper $helper)
    {
    }

    /**
     * Validates if number of returned items is not greater than quantity ordered.
     *
     * @param            $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ReturnItem) {
            return;
        }

        $orderItem = $value->getOrderItem();

        /*
         * If no order item is defined for return item, it can not be validated because it is being validated just by
         * itself.
         */
        if (!$orderItem) {
            return;
        }

        /*
         * Reduce all return items into a sum of their quantities and add validated item quantity.
         * Get previous returned items and get the quantity of all and reduce them to a single value
         */
        $returnedQuantity = array_reduce(
            $orderItem->getReturnItems()->toArray(),
            function ($carry, ReturnItem $item) use ($constraint, $value) {
                if ((!$constraint->includeSelf) && ($item === $value)) {
                    return $carry;
                }
                return $carry + $item->getQuantity();
            },
            0
        );

        // total returned quantity (previous returned quantity + currently returned quantity)
        $returnedQuantity += $value->getQuantity();

        /*
         * If returned quantity is greater than shipped, create a constraint violation.
         */
        if ($returnedQuantity > $this->helper->getOrderItemShippedQuantity($orderItem)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('quantity')
                ->addViolation();
        }
    }
}
