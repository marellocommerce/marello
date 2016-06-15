<?php

namespace Marello\Component\Inventory\InventoryAllocation;

use Doctrine\Common\Collections\Collection;
use Marello\Component\Inventory\InventoryAllocationInterface;

interface AllocationTargetInterface
{
    /**
     * @return Collection|InventoryAllocationInterface[]
     */
    public function getInventoryAllocations();

    /**
     * Returns name of property, that this entity is mapped to InventoryAllocation under.
     *
     * @return string
     */
    public static function getAllocationPropertyName();
}
