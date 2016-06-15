<?php

namespace Marello\Component\Inventory;

use Marello\Component\Order\OrderItemInterface;

interface InventoryAllocationInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return InventoryItemInterface
     */
    public function getInventoryItem();

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @return OrderItemInterface
     */
    public function getTargetOrderItem();

    /**
     * @param InventoryItemInterface $inventoryItem
     *
     * @return $this
     */
    public function setInventoryItem(InventoryItemInterface $inventoryItem);

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity);

    /**
     * @param OrderItemInterface $targetOrderItem
     *
     * @return $this
     */
    public function setTargetOrderItem(OrderItemInterface $targetOrderItem = null);
}