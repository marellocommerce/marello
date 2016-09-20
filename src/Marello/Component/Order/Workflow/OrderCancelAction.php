<?php

namespace Marello\Component\Order\Workflow;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Marello\Component\Inventory\Entity\InventoryItem;
use Marello\Component\Inventory\Logging\InventoryLoggerInterface;
use Marello\Component\Inventory\Model\InventoryLogInterface;
use Marello\Component\Order\OrderInterface;
use Marello\Component\Order\OrderItemInterface;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Model\ContextAccessor;

class OrderCancelAction extends OrderTransitionAction
{
    /** @var Registry */
    protected $doctrine;

    /** @var InventoryLoggerInterface */
    protected $logger;

    /** @var InventoryItem[] */
    protected $changedInventory;

    /**
     * OrderShipAction constructor.
     *
     * @param ContextAccessor $contextAccessor
     * @param Registry        $doctrine
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
        /** @var OrderInterface $order */
        $order = $context->getEntity();

        $this->changedInventory = [];

        $order->getItems()->map(function (OrderItemInterface $orderItem) {
            $this->cancelOrderItem($orderItem);
        });

        /*
         * Log all changed inventory.
         */
        $this->logger->log(
            $this->changedInventory,
            'order_workflow.cancelled',
            function (InventoryLogInterface $log) use ($order) {
                $log->setOrder($order);
            }
        );

        $this->doctrine->getManager()->flush();
    }

    /**
     * Deallocates all inventory allocated towards item (items have not been shipped and allocation was released).
     *
     * @param OrderItemInterface $orderItem
     */
    protected function cancelOrderItem(OrderItemInterface $orderItem)
    {
        $allocations = $orderItem->getInventoryAllocations();

        foreach ($allocations as $allocation) {
            $this->changedInventory[] = $inventoryItem = $allocation->getInventoryItem();

            /*
             * When allocation is removed, the allocated amount on inventory amount will be automatically decreased.
             */
            $this->doctrine->getManager()->remove($allocation);
            $this->doctrine->getManager()->persist($inventoryItem);
        }
    }
}
