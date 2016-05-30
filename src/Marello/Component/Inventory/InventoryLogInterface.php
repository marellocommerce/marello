<?php

namespace Marello\Component\Inventory;

use Marello\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\UserBundle\Entity\User;

interface InventoryLogInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getActionType();

    /**
     * @return User
     */
    public function getUser();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser($user);

    /**
     * @return InventoryItemInterface
     */
    public function getInventoryItem();

    /**
     * @return int
     */
    public function getOldQuantity();

    /**
     * @param int $oldQuantity
     *
     * @return $this
     */
    public function setOldQuantity($oldQuantity);

    /**
     * @return int
     */
    public function getNewQuantity();

    /**
     * @param int $newQuantity
     *
     * @return $this
     */
    public function setNewQuantity($newQuantity);

    /**
     * @return int
     */
    public function getOldAllocatedQuantity();

    /**
     * @param int $oldAllocatedQuantity
     *
     * @return $this
     */
    public function setOldAllocatedQuantity($oldAllocatedQuantity);

    /**
     * @return int
     */
    public function getNewAllocatedQuantity();

    /**
     * @param int $newAllocatedQuantity
     *
     * @return $this
     */
    public function setNewAllocatedQuantity($newAllocatedQuantity);

    /**
     * @return Order
     */
    public function getOrder();

    /**
     * @param Order $order
     *
     * @return $this
     */
    public function setOrder(Order $order);
}