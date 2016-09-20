<?php

namespace Marello\Component\Order\Workflow;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Marello\Component\Order\Entity\Order;
use Marello\Component\Order\Entity\OrderItem;
use Marello\Component\Inventory\Model\InventoryItemInterface;
use Marello\Component\Inventory\Model\InventoryLogInterface;
use Marello\Component\Inventory\Logging\InventoryLoggerInterface;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Model\ContextAccessor;

class OrderShipAction extends OrderTransitionAction
{
    /** @var Registry */
    protected $doctrine;

    /** @var InventoryLoggerInterface */
    protected $logger;

    /** @var InventoryItemInterface[] */
    protected $changedInventory;

    /**
     * OrderShipAction constructor.
     *
     * @param ContextAccessor          $contextAccessor
     * @param Registry                 $doctrine
     * @param InventoryLoggerInterface $logger
     */
    public function __construct(ContextAccessor $contextAccessor, Registry $doctrine, InventoryLoggerInterface $logger)
    {
        parent::__construct($contextAccessor);

        $this->doctrine = $doctrine;
        $this->logger   = $logger;
    }

    /**
     * @param WorkflowItem|mixed $context
     */
    protected function executeAction($context)
    {
        /** @var Order $order */
        $order = $context->getEntity();

        $this->changedInventory = [];

        $order->getItems()->map(function (OrderItem $orderItem) {
            $this->shipOrderItem($orderItem);
        });

        /*
         * Log all changed inventory.
         */
        $this->logger->log(
            $this->changedInventory,
            'order_workflow.shipped',
            function (InventoryLogInterface $log) use ($order) {
                $log->setOrder($order);
            }
        );

        $this->doctrine->getManager()->flush();
    }

    /**
     * Deallocates all items allocated to this item and reduces real stock, indicating that item has been shipped.
     *
     * @param OrderItem $orderItem
     */
    protected function shipOrderItem(OrderItem $orderItem)
    {
        $allocations = $orderItem->getInventoryAllocations();

        foreach ($allocations as $allocation) {
            $this->changedInventory[] = $inventoryItem = $allocation->getInventoryItem();

            /*
             * Reduce inventory item real stock by allocated amount.
             */
            $inventoryItem->modifyQuantity(-$allocation->getQuantity());

            /*
             * When allocation is removed, the allocated amount on inventory amount will be automatically decreased.
             */
            $this->doctrine->getManager()->remove($allocation);
            $this->doctrine->getManager()->persist($inventoryItem);
        }
    }
}
