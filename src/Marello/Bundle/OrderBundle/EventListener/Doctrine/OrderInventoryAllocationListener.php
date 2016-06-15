<?php

namespace Marello\Bundle\OrderBundle\EventListener\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Component\Inventory\InventoryAllocation\InventoryAllocator;
use Marello\Component\Inventory\InventoryAllocation\InventoryAllocatorInterface;
use Marello\Component\Inventory\Logging\InventoryLoggerInterface;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Component\Inventory\InventoryLogInterface;
use Marello\Component\Order\OrderItemInterface;

class OrderInventoryAllocationListener
{
    /** @var InventoryAllocator */
    protected $allocator;

    /** @var InventoryLoggerInterface */
    protected $logger;

    /**
     * OrderInventoryAllocationListener constructor.
     *
     * @param InventoryAllocatorInterface $allocator
     * @param InventoryLoggerInterface    $logger
     */
    public function __construct(InventoryAllocatorInterface $allocator, InventoryLoggerInterface $logger)
    {
        $this->allocator = $allocator;
        $this->logger    = $logger;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Order) {
            return;
        }

        $loggedItems = [];

        foreach ($entity->getItems() as $item) {
            $loggedItems[] = $inventoryItem = $this->getInventoryItemToAllocate($item, $args->getEntityManager());
            $this->allocator->allocate($inventoryItem, $item->getQuantity(), $item);
        }

        $this->logger->log(
            $loggedItems,
            'order_workflow.pending',
            function (InventoryLogInterface $log) use ($entity) {
                $log->setOrder($entity);
            }
        );
    }

    /**
     * @param OrderItemInterface $item
     * @param EntityManager      $em
     *
     * @return InventoryItem
     */
    protected function getInventoryItemToAllocate(OrderItemInterface $item, EntityManager $em)
    {
        $warehouse = $em
            ->getRepository('MarelloInventoryBundle:Warehouse')
            ->getDefault();

        return $em
            ->getRepository('MarelloInventoryBundle:InventoryItem')
            ->findOrCreateByWarehouseAndProduct($warehouse, $item->getProduct());
    }
}
