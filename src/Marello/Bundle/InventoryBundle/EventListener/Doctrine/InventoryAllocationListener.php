<?php

namespace Marello\Bundle\InventoryBundle\EventListener\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Marello\Component\Inventory\Model\InventoryAllocationInterface;

class InventoryAllocationListener
{

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof InventoryAllocationInterface) {
            return;
        }

        $inventoryItem = $entity->getInventoryItem();
        $inventoryItem->modifyAllocatedQuantity($entity->getQuantity());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof InventoryAllocationInterface) {
            return;
        }

        $entity->getInventoryItem()->modifyAllocatedQuantity(-$entity->getQuantity());
        $args->getEntityManager()->persist($entity->getInventoryItem());
    }
}
