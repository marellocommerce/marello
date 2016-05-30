<?php

namespace Marello\Component\Inventory;

use Marello\Bundle\OrderBundle\Entity\OrderItem;

interface InventoryAllocationRepositoryInterface
{
    /**
     * @param InventoryItemInterface $inventoryItem
     * @param OrderItem              $orderItem
     *
     * @return null|InventoryAllocationInterface
     */
    public function findOneByInventoryItemAndOrderItem(InventoryItemInterface $inventoryItem, OrderItem $orderItem);
}