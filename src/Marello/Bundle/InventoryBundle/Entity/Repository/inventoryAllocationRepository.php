<?php

namespace Marello\Bundle\InventoryBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Marello\Component\Inventory\InventoryAllocationInterface;
use Marello\Component\Inventory\InventoryAllocationRepositoryInterface;
use Marello\Component\Inventory\InventoryItemInterface;
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
