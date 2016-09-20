<?php

namespace Marello\Bundle\OrderBundle\EventListener\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Marello\Component\Inventory\InventoryAllocation\InventoryAllocatorInterface;
use Marello\Component\Inventory\Logging\InventoryLoggerInterface;
use Marello\Component\Inventory\Model\InventoryItemInterface;
use Marello\Component\Inventory\Model\InventoryLogInterface;
use Marello\Component\Inventory\Repository\InventoryItemRepositoryInterface;
use Marello\Component\Inventory\Repository\WarehouseRepositoryInterface;
use Marello\Component\Order\Model\OrderInterface;
use Marello\Component\Order\Model\OrderItemInterface;

class OrderInventoryAllocationListener
{
    /**
     * @var InventoryAllocatorInterface
     */
    protected $allocator;

    /**
     * @var InventoryLoggerInterface
     */
    protected $logger;

    /**
     * @var WarehouseRepositoryInterface
     */
    protected $warehouseRepository;

    /**
     * @var InventoryItemRepositoryInterface
     */
    protected $inventoryItemRepository;

    /**
     * OrderInventoryAllocationListener constructor.
     *
     * @param InventoryAllocatorInterface      $allocator
     * @param InventoryLoggerInterface         $logger
     */
    public function __construct(
        InventoryAllocatorInterface $allocator,
        InventoryLoggerInterface $logger
    ) {
        $this->allocator = $allocator;
        $this->logger    = $logger;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof OrderInterface) {
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
     * @param OrderItemInterface     $item
     * @param EntityManagerInterface $em
     *
     * @return InventoryItemInterface
     */
    protected function getInventoryItemToAllocate(OrderItemInterface $item, EntityManagerInterface $em)
    {
        $warehouse = $em
            ->getRepository('MarelloInventoryBundle:Warehouse')
            ->getDefault();

        return $em
            ->getRepository('MarelloInventoryBundle:InventoryItem')
            ->findOrCreateByWarehouseAndProduct($warehouse, $item->getProduct());
    }
}
