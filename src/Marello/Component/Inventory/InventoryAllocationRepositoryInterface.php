<?php

namespace Marello\Component\Inventory;

use Marello\Component\Order\OrderItemInterface;

interface InventoryAllocationRepositoryInterface
{
    /**
     * @param InventoryItemInterface $inventoryItem
     * @param OrderItemInterface     $orderItem
     *
     * @return null|InventoryAllocationInterface
     */
    public function findOneByInventoryItemAndOrderItem(InventoryItemInterface $inventoryItem, OrderItemInterface $orderItem);
}