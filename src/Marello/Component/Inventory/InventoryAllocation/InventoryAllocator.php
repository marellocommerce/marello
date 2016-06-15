<?php

namespace Marello\Component\Inventory\InventoryAllocation;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Marello\Bundle\InventoryBundle\Entity\InventoryAllocation;
use Marello\Component\Inventory\InventoryAllocation\AllocationTargetInterface;
use Marello\Component\Inventory\InventoryAllocationInterface;
use Marello\Component\Inventory\InventoryItemInterface;
use Oro\Component\PropertyAccess\PropertyAccessor;

class InventoryAllocator implements InventoryAllocatorInterface
{
    /** @var Registry */
    protected $doctrine;

    /**
     * InventoryAllocator constructor.
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
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
     * @return EntityManager
     */
    protected function manager()
    {
        return $this->doctrine
            ->getManager();
    }
}
