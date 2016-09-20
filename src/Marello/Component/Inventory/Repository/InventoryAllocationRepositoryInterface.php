<?php

namespace Marello\Component\Inventory\Repository;

use Marello\Component\Inventory\Model\InventoryAllocationInterface;
use Marello\Component\Inventory\Model\InventoryItemInterface;
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
