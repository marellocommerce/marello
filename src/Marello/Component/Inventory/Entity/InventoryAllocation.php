<?php

namespace Marello\Component\Inventory\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Component\Inventory\Model\InventoryAllocationInterface;
use Marello\Component\Inventory\Model\InventoryItemInterface;
use Marello\Component\Order\OrderItemInterface;

class InventoryAllocation implements InventoryAllocationInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var InventoryItemInterface
     */
    protected $inventoryItem;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var OrderItemInterface
     */
    protected $targetOrderItem = null;

    /**
     * InventoryAllocation constructor.
     *
     * @param InventoryItemInterface $inventoryItem
     * @param int                    $quantity
     */
    public function __construct(InventoryItemInterface $inventoryItem, $quantity)
    {
        $this->inventoryItem = $inventoryItem;
        $this->quantity      = $quantity;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return InventoryItemInterface
     */
    public function getInventoryItem()
    {
        return $this->inventoryItem;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return OrderItemInterface
     */
    public function getTargetOrderItem()
    {
        return $this->targetOrderItem;
    }

    /**
     * @param InventoryItemInterface $inventoryItem
     *
     * @return $this
     */
    public function setInventoryItem(InventoryItemInterface $inventoryItem)
    {
        $this->inventoryItem = $inventoryItem;

        return $this;
    }

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @param OrderItemInterface $targetOrderItem
     *
     * @return $this
     */
    public function setTargetOrderItem(OrderItemInterface $targetOrderItem = null)
    {
        $this->targetOrderItem = $targetOrderItem;

        return $this;
    }
}
