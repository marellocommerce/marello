<?php

namespace Marello\Component\Inventory\InventoryAllocation;

use Marello\Component\Inventory\Model\InventoryAllocationInterface;
use Marello\Component\Inventory\Model\InventoryItemInterface;

interface InventoryAllocatorInterface
{
    /**
     * Allocates inventory to given target.
     * Also modifies inventory item.
     *
     * @param InventoryItemInterface    $item
     * @param int                       $amount
     * @param AllocationTargetInterface $target Target entity
     */
    public function allocate(InventoryItemInterface $item, $amount, AllocationTargetInterface $target);

    /**
     * Deallocates inventory.
     * Also modifies inventory item.
     *
     * @param InventoryAllocationInterface $allocation
     */
    public function deallocate(InventoryAllocationInterface $allocation);
}
