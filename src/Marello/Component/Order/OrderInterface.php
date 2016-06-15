<?php

namespace Marello\Component\Order;

use Doctrine\Common\Collections\Collection;
use Marello\Component\Address\AddressInterface;
use Marello\Component\Pricing\PriceInterface;
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
     * @return PriceInterface
     */
    public function getSubtotal();

    /**
     * @param PriceInterface $subtotal
     *
     * @return $this
     */
    public function setSubtotal(PriceInterface $subtotal);

    /**
     * @return PriceInterface
     */
    public function getTotalTax();

    /**
     * @param PriceInterface $totalTax
     *
     * @return $this
     */
    public function setTotalTax(PriceInterface $totalTax);

    /**
     * @return PriceInterface
     */
    public function getGrandTotal();

    /**
     * @param PriceInterface $grandTotal
     *
     * @return $this
     */
    public function setGrandTotal(PriceInterface $grandTotal);

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
     * @return PriceInterface
     */
    public function getShippingAmount();

    /**
     * @param PriceInterface $shippingAmount
     *
     * @return $this
     */
    public function setShippingAmount(PriceInterface $shippingAmount);

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
     * @return PriceInterface
     */
    public function getDiscountAmount();

    /**
     * @param PriceInterface $discountAmount
     *
     * @return $this
     */
    public function setDiscountAmount(PriceInterface $discountAmount);

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