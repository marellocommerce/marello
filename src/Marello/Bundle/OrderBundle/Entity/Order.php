<?php

namespace Marello\Bundle\OrderBundle\Entity;

use Brick\Math\BigDecimal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\OrderBundle\Model\ExtendOrder;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Component\Address\AddressInterface;
use Marello\Component\Order\OrderInterface;
use Marello\Component\Order\OrderItemInterface;
use Marello\Component\Pricing\PriceInterface;
use Marello\Component\Sales\SalesChannelInterface;
use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;

/**
 * @ORM\Entity(repositoryClass="Marello\Bundle\OrderBundle\Entity\Repository\OrderRepository")
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
 * @ORM\Table(
 *      name="marello_order_order",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(columns={"order_reference", "saleschannel_id"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class Order extends ExtendOrder implements OrderInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="order_number",type="string", unique=true, nullable=true)
     */
    protected $orderNumber;

    /**
     * @var int
     *
     * @ORM\Column(name="order_reference",type="string", nullable=true)
     */
    protected $orderReference;

    /**
     * @var BigDecimal
     *
     * @ORM\Column(name="subtotal",type="money")
     */
    protected $subtotal = 0;

    /**
     * @var BigDecimal
     *
     * @ORM\Column(name="total_tax",type="money")
     */
    protected $totalTax = 0;

    /**
     * @var BigDecimal
     *
     * @ORM\Column(name="grand_total",type="money")
     */
    protected $grandTotal = 0;

    /**
     * @var string
     * @ORM\Column(name="currency", type="string", length=10, nullable=true)
     */
    protected $currency;

    /**
     * @var string
     * @ORM\Column(name="payment_method", type="string", length=255, nullable=true)
     */
    protected $paymentMethod;

    /**
     * @var string
     * @ORM\Column(name="payment_reference", type="string", length=255, nullable=true)
     */
    protected $paymentReference;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_details", type="text", nullable=true)
     */
    protected $paymentDetails;

    /**
     * @var BigDecimal
     *
     * @ORM\Column(name="shipping_amount", type="money", nullable=true)
     */
    protected $shippingAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_method", type="string", nullable=true)
     */
    protected $shippingMethod;

    /**
     * @var BigDecimal
     *
     * @ORM\Column(name="discount_amount", type="money", nullable=true)
     */
    protected $discountAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="discount_percent", type="percent", nullable=true)
     */
    protected $discountPercent;

    /**
     * @var string
     * @ORM\Column(name="coupon_code", type="string", length=255, nullable=true)
     */
    protected $couponCode;

    /**
     * @var Collection|OrderItem[]
     *
     * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="order", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $items;

    /**
     * @var AbstractAddress
     *
     * @ORM\OneToOne(targetEntity="Marello\Bundle\AddressBundle\Entity\Address", cascade={"persist", "remove"})
     */
    protected $billingAddress;

    /**
     * @var AbstractAddress
     *
     * @ORM\OneToOne(targetEntity="Marello\Bundle\AddressBundle\Entity\Address", cascade={"persist", "remove"})
     */
    protected $shippingAddress;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
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
     * @ORM\Column(name="updated_at", type="datetime")
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
     *
     * @ORM\Column(name="invoiced_at", type="datetime", nullable=true)
     */
    protected $invoicedAt;

    /**
     * @var SalesChannel
     *
     * @ORM\ManyToOne(targetEntity="Marello\Bundle\SalesBundle\Entity\SalesChannel")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    protected $salesChannel;

    /**
     * @var string
     *
     * @ORM\Column(name="saleschannel_name",type="string", nullable=false)
     */
    protected $salesChannelName;

    /**
     * @var WorkflowItem
     *
     * @ORM\OneToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowItem")
     * @ORM\JoinColumn(name="workflow_item_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $workflowItem;

    /**
     * @var WorkflowStep
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowStep")
     * @ORM\JoinColumn(name="workflow_step_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $workflowStep;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", nullable=false)
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

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * @ORM\PrePersist
     */
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
