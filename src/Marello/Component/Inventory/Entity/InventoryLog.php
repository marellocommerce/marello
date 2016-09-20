<?php

namespace Marello\Component\Inventory\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Order\Entity\Order;
use Marello\Component\Inventory\Model\InventoryItemInterface;
use Marello\Component\Inventory\Model\InventoryLogInterface;
use Marello\Component\Order\Model\OrderInterface;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Represents changes in inventory items over time.
 */
class InventoryLog implements InventoryLogInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $oldQuantity;

    /**
     * @var int
     */
    protected $newQuantity;

    /**
     * @var int
     */
    protected $oldAllocatedQuantity;

    /**
     * @var int
     */
    protected $newAllocatedQuantity;

    /**
     * @var string
     */
    protected $actionType;

    /**
     * @var User
     */
    protected $user = null;

    /**
     * @var Order
     */
    protected $order = null;

    /**
     * @var InventoryItemInterface
     */
    protected $inventoryItem;

    /**
     * @var \DateTime
     */
    protected $createdAt = null;

    /**
     * InventoryLog constructor.
     *
     * @param InventoryItemInterface $inventoryItem
     * @param string                 $trigger
     */
    public function __construct(InventoryItemInterface $inventoryItem, $trigger)
    {
        $this->inventoryItem = $inventoryItem;
        $this->actionType    = $trigger;

        $this->oldQuantity          = $this->newQuantity = $inventoryItem->getQuantity();
        $this->oldAllocatedQuantity = $this->newAllocatedQuantity = $inventoryItem->getAllocatedQuantity();
    }

    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
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
    public function getOldQuantity()
    {
        return $this->oldQuantity;
    }

    /**
     * @param int $oldQuantity
     *
     * @return $this
     */
    public function setOldQuantity($oldQuantity)
    {
        $this->oldQuantity = $oldQuantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getNewQuantity()
    {
        return $this->newQuantity;
    }

    /**
     * @param int $newQuantity
     *
     * @return $this
     */
    public function setNewQuantity($newQuantity)
    {
        $this->newQuantity = $newQuantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getOldAllocatedQuantity()
    {
        return $this->oldAllocatedQuantity;
    }

    /**
     * @param int $oldAllocatedQuantity
     *
     * @return $this
     */
    public function setOldAllocatedQuantity($oldAllocatedQuantity)
    {
        $this->oldAllocatedQuantity = $oldAllocatedQuantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getNewAllocatedQuantity()
    {
        return $this->newAllocatedQuantity;
    }

    /**
     * @param int $newAllocatedQuantity
     *
     * @return $this
     */
    public function setNewAllocatedQuantity($newAllocatedQuantity)
    {
        $this->newAllocatedQuantity = $newAllocatedQuantity;

        return $this;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param OrderInterface $order
     *
     * @return $this
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;

        return $this;
    }
}
