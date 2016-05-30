<?php

namespace Marello\Bundle\InventoryBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Component\Inventory\InventoryAllocationRepositoryInterface;
use Marello\Component\Inventory\InventoryItemInterface;

class InventoryAllocationRepository extends EntityRepository
    implements InventoryAllocationRepositoryInterface
{
    /**
     * @param InventoryItemInterface $inventoryItem
     * @param OrderItem              $orderItem
     *
     * @return null|object
     */
    public function findOneByInventoryItemAndOrderItem(InventoryItemInterface $inventoryItem, OrderItem $orderItem)
    {
        return $this->findOneBy(compact('inventoryItem', 'orderItem'));
    }
}
