<?php

namespace Marello\Component\Inventory\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Marello\Component\Inventory\Model\InventoryAllocationInterface;
use Marello\Component\Inventory\Model\InventoryItemInterface;
use Marello\Component\Inventory\Repository\InventoryAllocationRepositoryInterface;
use Marello\Component\Order\OrderItemInterface;

class InventoryAllocationRepository extends EntityRepository
    implements InventoryAllocationRepositoryInterface
{
    /**
     * @param InventoryItemInterface $inventoryItem
     * @param OrderItemInterface     $orderItem
     *
     * @return null|object|InventoryAllocationInterface
     */
    public function findOneByInventoryItemAndOrderItem(InventoryItemInterface $inventoryItem, OrderItemInterface $orderItem)
    {
        return $this->findOneBy(compact('inventoryItem', 'orderItem'));
    }
}
