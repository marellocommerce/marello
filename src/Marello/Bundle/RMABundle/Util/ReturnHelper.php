<?php

namespace Marello\Bundle\RMABundle\Util;

use Marello\Component\Order\Entity\OrderItem;
use Marello\Component\RMA\Entity\ReturnItem;

class ReturnHelper
{
    /**
     * Returns amount of already returned items for given order item.
     *
     * @param OrderItem $orderItem
     *
     * @return int
     */
    public function getOrderItemReturnedQuantity(OrderItem $orderItem)
    {
        $sum = 0;

        $orderItem
            ->getReturnItems()
            ->map(function (ReturnItem $returnItem) use (&$sum) {
                $sum += $returnItem->getQuantity();
            });

        return $sum;
    }
}
