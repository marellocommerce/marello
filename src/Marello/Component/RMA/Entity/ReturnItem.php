<?php

namespace Marello\Component\RMA\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Order\Model\OrderItemInterface;
use Marello\Component\RMA\Model\ExtendReturnItem;
use Marello\Component\RMA\Model\ReturnEntityInterface;
use Marello\Component\RMA\Model\ReturnItemInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

/**
 * @Oro\Config()
 */
class ReturnItem extends ExtendReturnItem implements ReturnItemInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ReturnEntityInterface
     */
    protected $return;

    /**
     * @var OrderItemInterface
     */
    protected $orderItem;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var \DateTime
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.created_at"
     *          }
     *      }
     * )
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.updated_at"
     *          }
     *      }
     * )
     */
    protected $updatedAt;

    /**
     * ReturnItemInterface constructor.
     *
     * @param OrderItemInterface $orderItem
     */
    public function __construct(OrderItemInterface $orderItem = null)
    {
        $this->orderItem = $orderItem;
    }

    /**
     * Copies product sku and name to attributes within this return item.
     */
    public function prePersist()
    {
        $this->createdAt = $this->updatedAt = new \DateTime();
    }

    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return ReturnEntityInterface
     */
    public function getReturn()
    {
        return $this->return;
    }

    /**
     * @param ReturnEntityInterface $return
     *
     * @return $this
     */
    public function setReturn(ReturnEntityInterface $return)
    {
        $this->return = $return;

        return $this;
    }

    /**
     * @return OrderItemInterface
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param OrderItemInterface $orderItem
     *
     * @return $this
     */
    public function setOrderItem(OrderItemInterface $orderItem)
    {
        $this->orderItem = $orderItem;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
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
     * Get currency for returnItem from "sibling" orderItem
     */
    public function getCurrency()
    {
        return $this->orderItem->getCurrency();
    }
}
