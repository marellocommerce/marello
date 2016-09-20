<?php

namespace Marello\Component\RMA\Model;

use Marello\Component\Order\Model\OrderItemInterface;
use Marello\Component\Pricing\Model\CurrencyAwareInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

interface ReturnItemInterface extends CurrencyAwareInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @return ReturnEntityInterface
     */
    public function getReturn();

    /**
     * @param ReturnEntityInterface $return
     *
     * @return $this
     */
    public function setReturn(ReturnEntityInterface $return);

    /**
     * @return OrderItemInterface
     */
    public function getOrderItem();

    /**
     * @param OrderItemInterface $orderItem
     *
     * @return $this
     */
    public function setOrderItem(OrderItemInterface $orderItem);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity);
}
