<?php

namespace Marello\Component\Order;

use Brick\Math\BigNumber;
use Doctrine\Common\Collections\Collection;
use Marello\Component\Address\AddressInterface;
use Marello\Component\Sales\SalesChannelInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;

interface OrderInterface
{
    /**
     * @return string
     */
    public function getOrderReference();

    /**
     * @param string $orderReference
     *
     * @return $this
     */
    public function setOrderReference($orderReference);

    /**
     * @return BigNumber
     */
    public function getSubtotal();

    /**
     * @param BigNumber $subtotal
     *
     * @return $this
     */
    public function setSubtotal(BigNumber $subtotal);

    /**
     * @return BigNumber
     */
    public function getTotalTax();

    /**
     * @param BigNumber $totalTax
     *
     * @return $this
     */
    public function setTotalTax(BigNumber $totalTax);

    /**
     * @return BigNumber
     */
    public function getGrandTotal();

    /**
     * @param BigNumber $grandTotal
     *
     * @return $this
     */
    public function setGrandTotal(BigNumber $grandTotal);

    /**
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * @param AddressInterface $shippingAddress
     *
     * @return $this
     */
    public function setShippingAddress(AddressInterface $shippingAddress);

    /**
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * @param AddressInterface $billingAddress
     *
     * @return $this
     */
    public function setBillingAddress(AddressInterface $billingAddress);

    /**
     * @return Collection|OrderItemInterface[]
     */
    public function getItems();

    /**
     * @param OrderItemInterface $item
     *
     * @return $this
     */
    public function addItem(OrderItemInterface $item);

    /**
     * @param OrderItemInterface $item
     *
     * @return $this
     */
    public function removeItem(OrderItemInterface $item);

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
     * @return SalesChannelInterface
     */
    public function getSalesChannel();

    /**
     * @param SalesChannelInterface $salesChannel
     *
     * @return $this
     */
    public function setSalesChannel(SalesChannelInterface $salesChannel);

    /**
     * @return string
     */
    public function getOrderNumber();

    /**
     * @param string $orderNumber
     *
     * @return $this
     */
    public function setOrderNumber($orderNumber);

    /**
     * @param WorkflowItem $workflowItem
     *
     * @return $this
     */
    public function setWorkflowItem(WorkflowItem $workflowItem);

    /**
     * @return WorkflowItem
     */
    public function getWorkflowItem();

    /**
     * @param WorkflowStep $workflowStep
     *
     * @return $this
     */
    public function setWorkflowStep(WorkflowStep $workflowStep);

    /**
     * @return WorkflowStep
     */
    public function getWorkflowStep();

    /**
     * @return string
     */
    public function getSalesChannelName();

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * @return string
     */
    public function getPaymentMethod();

    /**
     * @param string $paymentMethod
     *
     * @return $this
     */
    public function setPaymentMethod($paymentMethod);

    /**
     * @return string
     */
    public function getPaymentDetails();

    /**
     * @param string $paymentDetails
     *
     * @return $this
     */
    public function setPaymentDetails($paymentDetails);

    /**
     * @return BigNumber
     */
    public function getShippingAmount();

    /**
     * @param BigNumber $shippingAmount
     *
     * @return $this
     */
    public function setShippingAmount(BigNumber $shippingAmount);

    /**
     * @return string
     */
    public function getShippingMethod();

    /**
     * @param string $shippingMethod
     *
     * @return $this
     */
    public function setShippingMethod($shippingMethod);

    /**
     * @return BigNumber
     */
    public function getDiscountAmount();

    /**
     * @param BigNumber $discountAmount
     *
     * @return $this
     */
    public function setDiscountAmount(BigNumber $discountAmount);

    /**
     * @return float
     */
    public function getDiscountPercent();

    /**
     * @param float $discountPercent
     *
     * @return $this
     */
    public function setDiscountPercent($discountPercent);

    /**
     * @return string
     */
    public function getCouponCode();

    /**
     * @param string $couponCode
     */
    public function setCouponCode($couponCode);

    /**
     * @return \DateTime
     */
    public function getInvoicedAt();

    /**
     * @param \DateTime $invoicedAt
     *
     * @return $this
     */
    public function setInvoicedAt(\DateTime $invoicedAt);

    /**
     * @return string
     */
    public function getPaymentReference();

    /**
     * @param string $paymentReference
     *
     * @return $this
     */
    public function setPaymentReference($paymentReference);

    /**
     * @return OrganizationInterface
     */
    public function getOrganization();

    /**
     * @param OrganizationInterface $organization
     *
     * @return $this
     */
    public function setOrganization(OrganizationInterface $organization);
}
