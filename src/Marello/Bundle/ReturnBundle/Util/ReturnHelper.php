<?php

namespace Marello\Bundle\ReturnBundle\Util;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\ReturnBundle\Entity\ReturnItem;
use Marello\Bundle\InventoryBundle\Entity\AllocationItem;

class ReturnHelper
{
    /** @var DoctrineHelper $doctrineHelper */
    protected $doctrineHelper;

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
    /**
     * Returns amount of shipped for given order item.
     *
     * @param OrderItem $orderItem
     *
     * @return int
     */
    public function getOrderItemShippedQuantity(OrderItem $orderItem)
    {
        $quantityShipped = 0;
        $repo = $this->doctrineHelper->getEntityRepositoryForClass(AllocationItem::class);
        $items = $repo->findBy(['orderItem' => $orderItem]);

        /** @var AllocationItem $item */
        foreach ($items as $item) {
            $quantityShipped += $item->getQuantityConfirmed();
        }

        return $quantityShipped;
    }

    /**
     * @param DoctrineHelper $doctrineHelper
     * @return void
     */
    public function setDoctrineHelper(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }
}
