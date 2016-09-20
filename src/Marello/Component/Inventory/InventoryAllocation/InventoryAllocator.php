<?php

namespace Marello\Component\Inventory\InventoryAllocation;

use Doctrine\ORM\EntityManagerInterface;
use Marello\Component\Inventory\Entity\InventoryAllocation;
use Marello\Component\Inventory\Model\InventoryAllocationInterface;
use Marello\Component\Inventory\Model\InventoryItemInterface;
use Oro\Component\PropertyAccess\PropertyAccessor;
use Symfony\Bridge\Doctrine\RegistryInterface;

class InventoryAllocator implements InventoryAllocatorInterface
{
    /** @var RegistryInterface */
    protected $doctrine;

    /**
     * InventoryAllocator constructor.
     *
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Allocates inventory to given target.
     * Also modifies inventory item.
     *
     * @param InventoryItemInterface    $item
     * @param int                       $amount
     * @param AllocationTargetInterface $target Target entity
     */
    public function allocate(InventoryItemInterface $item, $amount, AllocationTargetInterface $target)
    {
        $allocation = new InventoryAllocation($item, $amount);
        $this->setAllocationTarget($allocation, $target);

        $this->manager()->persist($allocation);
    }

    /**
     * Deallocates inventory.
     * Also modifies inventory item.
     *
     * @param InventoryAllocationInterface $allocation
     */
    public function deallocate(InventoryAllocationInterface $allocation)
    {
        $this->manager()->remove($allocation);
    }

    /**
     * Sets allocation target on allocation entity.
     *
     * @param InventoryAllocationInterface $allocation
     * @param AllocationTargetInterface    $target
     */
    protected function setAllocationTarget(InventoryAllocationInterface $allocation, AllocationTargetInterface $target)
    {
        $pa = new PropertyAccessor();

        $pa->setValue(
            $allocation,
            sprintf('target%s', ucfirst($target->getAllocationPropertyName())),
            $target
        );
    }

    /**
     * @return EntityManagerInterface
     */
    protected function manager()
    {
        return $this->doctrine
            ->getManager();
    }
}
