<?php

namespace Marello\Bundle\InvoiceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Marello\Bundle\PaymentBundle\Entity\Payment;
use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Marello\Bundle\CoreBundle\DerivedProperty\DerivedPropertyAwareInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\PricingBundle\Model\CurrencyAwareInterface;
use Marello\Bundle\PricingBundle\Subtotal\Model\LineItemsAwareInterface;
use Marello\Bundle\PricingBundle\Subtotal\Model\SubtotalAwareInterface;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\SalesBundle\Model\SalesChannelAwareInterface;

/**
 * @Oro\Config(
 *      routeView="marello_invoice_invoice_view",
 *      routeName="marello_invoice_invoice_index",
 *      defaultValues={
 *          "entity"={
 *              "icon"="fa-file-invoice"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"=""
 *          },
 *          "ownership"={
 *              "owner_type"="ORGANIZATION",
 *              "owner_field_name"="organization",
 *              "owner_column_name"="organization_id"
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_invoice_invoice')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\InvoiceBundle\Entity\Repository\AbstractInvoiceRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['invoice' => 'Marello\Bundle\InvoiceBundle\Entity\Invoice', 'creditmemo' => 'Marello\Bundle\InvoiceBundle\Entity\Creditmemo'])]
#[ORM\HasLifecycleCallbacks]
abstract class AbstractInvoice implements
    DerivedPropertyAwareInterface,
    CurrencyAwareInterface,
    SubtotalAwareInterface,
    LineItemsAwareInterface,
    SalesChannelAwareInterface,
    OrganizationAwareInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected $id;

    /**
     * @var string
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "full"=true
     *          },
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'invoice_type', type: 'string', nullable: true)]
    protected $invoiceType;

    /**
     * @var string
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'invoice_number', type: 'string', unique: true, nullable: true)]
    protected $invoiceNumber;

    /**
     * @var MarelloAddress
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'billing_address_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\AddressBundle\Entity\MarelloAddress::class, cascade: ['persist'])]
    protected $billingAddress;

    /**
     * @var MarelloAddress
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'shipping_address_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\AddressBundle\Entity\MarelloAddress::class, cascade: ['persist'])]
    protected $shippingAddress;

    /**
     * @var \DateTime
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'invoiced_at', type: 'datetime', nullable: true)]
    protected $invoicedAt;

    /**
     * @var \DateTime
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'invoice_due_date', type: 'datetime', nullable: true)]
    protected $invoiceDueDate;

    /**
     * @var string
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'payment_method', type: 'string', length: 255, nullable: true)]
    protected $paymentMethod;

    /**
     * @var string
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'shipping_method', type: 'string', nullable: true)]
    protected $shippingMethod;

    /**
     * @var string
     */
    #[ORM\Column(name: 'shipping_method_type', type: 'string', length: 255, nullable: true)]
    protected $shippingMethodType;

    /**
     * @var Order
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "full"=true
     *          },
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(onDelete: 'cascade', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\OrderBundle\Entity\Order::class)]
    protected $order;

    /**
     * @var string
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'currency', type: 'string', length: 10, nullable: true)]
    protected $currency;

    /**
     * @var string
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'status', type: 'string', length: 10, nullable: true)]
    protected $status;

    /**
     * @var Customer
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\CustomerBundle\Entity\Customer::class, cascade: ['persist'])]
    protected $customer;

    /**
     * @var SalesChannel
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "full"=true
     *          },
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(onDelete: 'SET NULL', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\SalesBundle\Entity\SalesChannel::class)]
    protected $salesChannel;

    /**
     * @var string
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'saleschannel_name', type: 'string', nullable: true)]
    protected $salesChannelName;

    /**
     * @var Collection|AbstractInvoiceItem[]
     */
    protected $items;

    /**
     * @var Collection|Payment[]
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "email"={
     *              "available_in_template"=true
     *          },
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinTable(name: 'marello_invoice_payments')]
    #[ORM\JoinColumn(name: 'invoice_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'payment_id', referencedColumnName: 'id', unique: true, onDelete: 'CASCADE')]
    #[ORM\ManyToMany(targetEntity: \Marello\Bundle\PaymentBundle\Entity\Payment::class, cascade: ['persist'])]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $payments;

    /**
     * @var int
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'subtotal', type: 'money')]
    protected $subtotal = 0;

    /**
     * @var int
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'total_tax', type: 'money')]
    protected $totalTax = 0;

    /**
     * @var int
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'grand_total', type: 'money')]
    protected $grandTotal = 0;

    /**
     * @var double
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'shipping_amount_incl_tax', type: 'money', nullable: true)]
    protected $shippingAmountInclTax;

    /**
     * @var double
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'shipping_amount_excl_tax', type: 'money', nullable: true)]
    protected $shippingAmountExclTax;

    /**
     * @var int
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'total_due', type: 'money', nullable: true)]
    protected $totalDue = 0;

    /**
     * @var int
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'total_paid', type: 'money', nullable: true)]
    protected $totalPaid = 0;

    /**
     * @param AbstractAddress|null $billingAddress
     * @param AbstractAddress|null $shippingAddress
     */
    public function __construct(
        AbstractAddress $billingAddress = null,
        AbstractAddress $shippingAddress = null
    ) {
        $this->items = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
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
    public function getInvoiceType()
    {
        return $this->invoiceType;
    }

    /**
     * @param string $invoiceType
     * @return AbstractInvoice
     */
    public function setInvoiceType($invoiceType)
    {
        $this->invoiceType = $invoiceType;

        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     * @return AbstractInvoice
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * @return MarelloAddress
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param MarelloAddress $billingAddress
     * @return AbstractInvoice
     */
    public function setBillingAddress(MarelloAddress $billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * @return MarelloAddress
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param MarelloAddress $shippingAddress
     * @return AbstractInvoice
     */
    public function setShippingAddress(MarelloAddress $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
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
     * @return AbstractInvoice
     */
    public function setInvoicedAt(\DateTime $invoicedAt)
    {
        $this->invoicedAt = $invoicedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getInvoiceDueDate()
    {
        return $this->invoiceDueDate;
    }

    /**
     * @param \DateTime $invoiceDueDate
     * @return AbstractInvoice
     */
    public function setInvoiceDueDate(\DateTime $invoiceDueDate = null)
    {
        $this->invoiceDueDate = $invoiceDueDate;

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
     * @return AbstractInvoice
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

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
     * @return AbstractInvoice
     */
    public function setShippingMethod($shippingMethod)
    {
        $this->shippingMethod = $shippingMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getShippingMethodType()
    {
        return $this->shippingMethodType;
    }

    /**
     * @param string $shippingMethodType
     * @return AbstractInvoice
     */
    public function setShippingMethodType($shippingMethodType)
    {
        $this->shippingMethodType = $shippingMethodType;

        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return AbstractInvoice
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
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
     * @return AbstractInvoice
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return AbstractInvoice
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return AbstractInvoice
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return SalesChannel
     */
    public function getSalesChannel()
    {
        return $this->salesChannel;
    }

    /**
     * @param SalesChannel $salesChannel
     * @return AbstractInvoice
     */
    public function setSalesChannel(SalesChannel $salesChannel)
    {
        $this->salesChannel = $salesChannel;
        if ($this->salesChannel) {
            $this->salesChannelName = $this->salesChannel->getName();
        }
        return $this;
    }

    /**
     * @return Collection|AbstractInvoiceItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param AbstractInvoiceItem $item
     *
     * @return $this
     */
    public function addItem(AbstractInvoiceItem $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setInvoice($this);
        }

        return $this;
    }

    /**
     * @param AbstractInvoiceItem $item
     *
     * @return $this
     */
    public function removeItem(AbstractInvoiceItem $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }

        return $this;
    }

    /**
     * @return Collection|Payment[]
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param Payment $payment
     *
     * @return $this
     */
    public function addPayment(Payment $payment)
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $totalPaid = $this->getTotalPaid() ? : 0;
            $grandTotal = $this->getGrandTotal() ? : 0;

            $this->setTotalPaid($payment->getTotalPaid() + $totalPaid);
            $this->setTotalDue($grandTotal - $this->getTotalPaid());
        }

        return $this;
    }

    /**
     * @param Payment $payment
     *
     * @return $this
     */
    public function removePayment(Payment $payment)
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            $totalPaid = $this->getTotalPaid() ? : 0;
            $grandTotal = $this->getGrandTotal() ? : 0;

            $this->setTotalPaid($totalPaid - $payment->getTotalPaid());
            $this->setTotalDue($grandTotal - $this->getTotalPaid());
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @param int $subtotal
     * @return AbstractInvoice
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalTax()
    {
        return $this->totalTax;
    }

    /**
     * @param int $totalTax
     * @return AbstractInvoice
     */
    public function setTotalTax($totalTax)
    {
        $this->totalTax = $totalTax;

        return $this;
    }

    /**
     * @return int
     */
    public function getGrandTotal()
    {
        return $this->grandTotal;
    }

    /**
     * @param int $grandTotal
     * @return AbstractInvoice
     */
    public function setGrandTotal($grandTotal)
    {
        $this->grandTotal = $grandTotal;

        return $this;
    }

    /**
     * @return float
     */
    public function getShippingAmountInclTax()
    {
        return $this->shippingAmountInclTax;
    }

    /**
     * @param float $shippingAmountInclTax
     * @return AbstractInvoice
     */
    public function setShippingAmountInclTax($shippingAmountInclTax)
    {
        $this->shippingAmountInclTax = $shippingAmountInclTax;

        return $this;
    }

    /**
     * @return float
     */
    public function getShippingAmountExclTax()
    {
        return $this->shippingAmountExclTax;
    }

    /**
     * @param float $shippingAmountExclTax
     * @return AbstractInvoice
     */
    public function setShippingAmountExclTax($shippingAmountExclTax)
    {
        $this->shippingAmountExclTax = $shippingAmountExclTax;

        return $this;
    }

    /**
     * @param int $id
     */
    public function setDerivedProperty($id)
    {
        if (!$this->invoiceNumber) {
            $this->setInvoiceNumber(sprintf('%09d', $id));
        }
    }

    /**
     * @return float
     */
    public function getTotalDue()
    {
        return $this->totalDue;
    }

    /**
     * @param float $totalDue
     * @return $this
     */
    public function setTotalDue($totalDue)
    {
        $this->totalDue = $totalDue;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalPaid()
    {
        return $this->totalPaid;
    }

    /**
     * @param float $totalPaid
     * @return $this
     */
    public function setTotalPaid($totalPaid)
    {
        $this->totalPaid = $totalPaid;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('#%s', $this->invoiceNumber);
    }
}
