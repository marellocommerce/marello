<?php

namespace Marello\Bundle\OrderBundle\EventListener\Doctrine;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Marello\Component\Inventory\InventoryAllocation\InventoryAllocator;
use Marello\Component\Inventory\InventoryAllocation\InventoryAllocatorInterface;
use Marello\Component\Inventory\InventoryItemInterface;
use Marello\Component\Inventory\InventoryItemRepositoryInterface;
use Marello\Component\Inventory\Logging\InventoryLoggerInterface;
use Marello\Component\Inventory\InventoryLogInterface;
use Marello\Component\Inventory\WarehouseRepositoryInterface;
use Marello\Component\Order\OrderInterface;
use Marello\Component\Order\OrderItemInterface;

class OrderInventoryAllocationListener
{
    /**
     * @var InventoryAllocator
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
     * @param WarehouseRepositoryInterface     $warehouseRepository
     * @param InventoryItemRepositoryInterface $inventoryItemRepository
     */
    public function __construct(
        InventoryAllocatorInterface $allocator,
        InventoryLoggerInterface $logger,
        WarehouseRepositoryInterface $warehouseRepository,
        InventoryItemRepositoryInterface $inventoryItemRepository
    ) {
        $this->allocator               = $allocator;
        $this->logger                  = $logger;
        $this->warehouseRepository     = $warehouseRepository;
        $this->inventoryItemRepository = $inventoryItemRepository;
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
            $loggedItems[] = $inventoryItem = $this->getInventoryItemToAllocate($item);
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
     *
     * @return InventoryItemInterface
     */
    protected function getInventoryItemToAllocate(OrderItemInterface $item)
    {
        $warehouse = $this->warehouseRepository->getDefault();

        return $this->inventoryItemRepository
            ->findOrCreateByWarehouseAndProduct($warehouse, $item->getProduct());
    }
}
