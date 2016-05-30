<?php

namespace Marello\Component\Inventory;


use Marello\Bundle\OrderBundle\Entity\OrderItem;

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
     * @return OrderItem
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
     * @param OrderItem $targetOrderItem
     *
     * @return $this
     */
    public function setTargetOrderItem(OrderItem $targetOrderItem = null);
}