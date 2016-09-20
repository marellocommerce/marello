<?php

namespace Marello\Component\Order\Entity;

use Brick\Math\BigDecimal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Order\Model\ExtendOrder;
use Marello\Component\Sales\Entity\SalesChannel;
use Marello\Component\Address\Model\AddressInterface;
use Marello\Component\Order\Model\OrderInterface;
use Marello\Component\Order\Model\OrderItemInterface;
use Marello\Component\Pricing\Model\PriceInterface;
use Marello\Component\Sales\Model\SalesChannelInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;

/**
 * @Oro\Config(
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-list-alt"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"=""
 *          },
 *          "workflow"={
 *              "active_workflow"="marello_order_b2c_workflow_1"
 *          },
 *          "ownership"={
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          }
 *      }
 * )
 */
class Order extends ExtendOrder implements OrderInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $orderNumber;

    /**
     * @var int
     */
    protected $orderReference;

    /**
     * @var BigDecimal
     */
    protected $subtotal = 0;

    /**
     * @var BigDecimal
     */
    protected $totalTax = 0;

    /**
     * @var BigDecimal
     */
    protected $grandTotal = 0;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $paymentMethod;

    /**
     * @var string
     */
    protected $paymentReference;

    /**
     * @var string
     */
    protected $paymentDetails;

    /**
     * @var BigDecimal
     */
    protected $shippingAmount;

    /**
     * @var string
     */
    protected $shippingMethod;

    /**
     * @var BigDecimal
     */
    protected $discountAmount;

    /**
     * @var float
     */
    protected $discountPercent;

    /**
     * @var string
     */
    protected $couponCode;

    /**
     * @var Collection|OrderItemInterface[]
     */
    protected $items;

    /**
     * @var AddressInterface
     */
    protected $billingAddress;

    /**
     * @var AddressInterface
     */
    protected $shippingAddress;

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
     * @var \DateTime
     */
    protected $invoicedAt;

    /**
     * @var SalesChannelInterface
     */
    protected $salesChannel;

    /**
     * @var string
     */
    protected $salesChannelName;

    /**
     * @var WorkflowItem
     */
    protected $workflowItem;

    /**
     * @var WorkflowStep
     */
    protected $workflowStep;

    /**
     * @var OrganizationInterface
     */
    protected $organization;

    /**
     * @param AddressInterface|null $billingAddress
     * @param AddressInterface|null $shippingAddress
     */
    public function __construct(
        AddressInterface $billingAddress = null,
        AddressInterface $shippingAddress = null
    ) {
        $this->items           = new ArrayCollection();
        $this->billingAddress  = $billingAddress;
        $this->shippingAddress = $shippingAddress;
    }

    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    public function prePersist()
    {
        $this->createdAt        = $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->salesChannelName = $this->salesChannel->getName();
    }

    /**
     * @return string
     */
    public function getOrderReference()
    {
        return $this->orderReference;
    }

    /**
     * @param string $orderReference
     *
     * @return $this
     */
    public function setOrderReference($orderReference)
    {
        $this->orderReference = $orderReference;

        return $this;
    }

    /**
     * @return BigDecimal
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @param BigDecimal $subtotal
     *
     * @return $this
     */
    public function setSubtotal(BigDecimal $subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalTax()
    {
        return $this->totalTax;
    }

    /**
     * @param BigDecimal $totalTax
     *
     * @return $this
     */
    public function setTotalTax(BigDecimal $totalTax)
    {
        $this->totalTax = $totalTax;

        return $this;
    }

    /**
     * @return PriceInterface
     */
    public function getGrandTotal()
    {
        return $this->grandTotal;
    }

    /**
     * @param BigDecimal $grandTotal
     *
     * @return $this
     */
    public function setGrandTotal(BigDecimal $grandTotal)
    {
        $this->grandTotal = $grandTotal;

        return $this;
    }

    /**
     * @return AddressInterface
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param AddressInterface $shippingAddress
     *
     * @return $this
     */
    public function setShippingAddress(AddressInterface $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * @return AddressInterface
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param AddressInterface $billingAddress
     *
     * @return $this
     */
    public function setBillingAddress(AddressInterface $billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * @return Collection|OrderItemInterface[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param OrderItemInterface $item
     *
     * @return $this
     */
    public function addItem(OrderItemInterface $item)
    {
        $this->items->add($item);
        $item->setOrder($this);

        return $this;
    }

    /**
     * @param OrderItemInterface $item
     *
     * @return $this
     */
    public function removeItem(OrderItemInterface $item)
    {
        $this->items->removeElement($item);

        return $this;
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
     * @return SalesChannelInterface
     */
    public function getSalesChannel()
    {
        return $this->salesChannel;
    }

    /**
     * @param SalesChannelInterface $salesChannel
     *
     * @return $this
     */
    public function setSalesChannel(SalesChannelInterface $salesChannel)
    {
        $this->salesChannel = $salesChannel;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     *
     * @return $this
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * @param WorkflowItem $workflowItem
     *
     * @return $this
     */
    public function setWorkflowItem(WorkflowItem $workflowItem)
    {
        $this->workflowItem = $workflowItem;

        return $this;
    }

    /**
     * @return WorkflowItem
     */
    public function getWorkflowItem()
    {
        return $this->workflowItem;
    }

    /**
     * @param WorkflowStep $workflowStep
     *
     * @return $this
     */
    public function setWorkflowStep(WorkflowStep $workflowStep)
    {
        $this->workflowStep = $workflowStep;

        return $this;
    }

    /**
     * @return WorkflowStep
     */
    public function getWorkflowStep()
    {
        return $this->workflowStep;
    }

    /**
     * @return string
     */
    public function getSalesChannelName()
    {
        return $this->salesChannelName;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     *
     * @return $this
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }

    /**
     * @param string $paymentDetails
     *
     * @return $this
     */
    public function setPaymentDetails($paymentDetails)
    {
        $this->paymentDetails = $paymentDetails;

        return $this;
    }

    /**
     * @return PriceInterface
     */
    public function getShippingAmount()
    {
        return $this->shippingAmount;
    }

    /**
     * @param BigDecimal $shippingAmount
     *
     * @return $this
     */
    public function setShippingAmount(BigDecimal $shippingAmount)
    {
        $this->shippingAmount = $shippingAmount;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @param string $shippingMethod
     *
     * @return $this
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * @param BigDecimal $discountAmount
     *
     * @return $this
     */
    public function setDiscountAmount(BigDecimal $discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * @return PriceInterface
     */
    public function getDiscountPercent()
    {
        return $this->discountPercent;
    }

    /**
     * @param float $discountPercent
     *
     * @return $this
     */
    public function setDiscountPercent($discountPercent)
    {
        $this->discountPercent = $discountPercent;

        return $this;
    }

    /**
     * @return string
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * @param string $couponCode
     */
    public function setCouponCode($couponCode)
    {
        $this->couponCode = $couponCode;
    }

    /**
     * @return \DateTime
     */
    public function getInvoicedAt()
    {
        return $this->invoicedAt;
    }

    /**
     * @param \DateTime $invoicedAt
     *
     * @return $this
     */
    public function setInvoicedAt(\DateTime $invoicedAt)
    {
        $this->invoicedAt = $invoicedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentReference()
    {
        return $this->paymentReference;
    }

    /**
     * @param string $paymentReference
     *
     * @return $this
     */
    public function setPaymentReference($paymentReference)
    {
        $this->paymentReference = $paymentReference;

        return $this;
    }

    /**
     * @return OrganizationInterface
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param OrganizationInterface $organization
     *
     * @return $this
     */
    public function setOrganization(OrganizationInterface $organization)
    {
        $this->organization = $organization;

        return $this;
    }
}
