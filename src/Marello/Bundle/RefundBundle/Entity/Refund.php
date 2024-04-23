<?php

namespace Marello\Bundle\RefundBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CoreBundle\DerivedProperty\DerivedPropertyAwareInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\LocaleBundle\Model\LocalizationAwareInterface;
use Marello\Bundle\LocaleBundle\Model\LocalizationTrait;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Entity\OrderAwareInterface;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\PricingBundle\Model\CurrencyAwareInterface;
use Marello\Bundle\ReturnBundle\Entity\ReturnEntity;
use Marello\Bundle\ReturnBundle\Entity\ReturnItem;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;


#[ORM\Table(name: 'marello_refund')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(routeView: 'marello_refund_view', routeName: 'marello_refund_index', routeCreate: 'marello_refund_create', defaultValues: ['entity' => ['icon' => 'fa-eur'], 'security' => ['type' => 'ACL', 'group_name' => ''], 'ownership' => ['owner_type' => 'ORGANIZATION', 'owner_field_name' => 'organization', 'owner_column_name' => 'organization_id'], 'grid' => ['default' => 'marello-refund-select-grid'], 'dataaudit' => ['auditable' => true]])]
class Refund implements
    DerivedPropertyAwareInterface,
    CurrencyAwareInterface,
    LocalizationAwareInterface,
    OrderAwareInterface,
    ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;
    use LocalizationTrait;
    use ExtendEntityTrait;
        
    /**
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'refund_number', type: Types::STRING, unique: true, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $refundNumber;

    /**
     * @var float
     * technically the grandtotal
     */
    #[ORM\Column(name: 'refund_amount', type: 'money')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $refundAmount;

    /**
     * @var float
     */
    #[ORM\Column(name: 'refund_subtotal', type: 'money')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'entity' => ['label' => 'marello.refund.subtotal.label']])]
    protected $refundSubtotal = 0.00;

    /**
     * @var float
     */
    #[ORM\Column(name: 'refund_tax_total', type: 'money')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'entity' => ['label' => 'marello.refund.tax_total.label']])]
    protected $refundTaxTotal = 0.00;

    /**
     * @var Customer
     */
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\CustomerBundle\Entity\Customer::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $customer;

    /**
     * @var Collection|RefundItem[]
     */
    #[ORM\OneToMany(targetEntity: \RefundItem::class, mappedBy: 'refund', cascade: ['persist'], orphanRemoval: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $items;

    /**
     * @var Order
     */
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\OrderBundle\Entity\Order::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $order;

    /**
     * @var string
     */
    #[ORM\Column(name: 'currency', type: Types::STRING, length: 10, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $currency;

    /**
     * @param Order $order
     *
     * @return Refund
     */
    public static function fromOrder(Order $order)
    {
        $refund = new self();

        $refund
            ->setOrder($order)
            ->setCustomer($order->getCustomer())
            ->setOrganization($order->getOrganization())
            ->setCurrency($order->getCurrency())
            ->setLocalization($order->getLocalization())
            ->setRefundSubtotal($order->getSubtotal())
            ->setRefundTaxTotal($order->getTotalTax())
        ;

        $order->getItems()->map(
            function (OrderItem $item) use ($refund) {
                $refund->addItem(RefundItem::fromOrderItem($item));
            }
        );

        return $refund;
    }

    /**
     * @param ReturnEntity $return
     *
     * @return Refund
     */
    public static function fromReturn(ReturnEntity $return)
    {
        $refund = new self();

        $refund
            ->setOrder($return->getOrder())
            ->setCustomer($return->getOrder()->getCustomer())
            ->setOrganization($return->getOrganization())
            ->setCurrency($return->getOrder()->getCurrency())
            ->setLocalization($return->getOrder()->getLocalization())
            ->setRefundSubtotal($return->getOrder()->getSubtotal())
            ->setRefundTaxTotal($return->getOrder()->getTotalTax())
        ;

        $return->getReturnItems()->map(
            function (ReturnItem $item) use ($refund) {
                $refund->addItem(RefundItem::fromReturnItem($item));
            }
        );

        return $refund;
    }

    /**
     * Refund constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $sum = array_reduce($this->getItems()->toArray(), function ($carry, RefundItem $item) {
            return $carry + $item->getRefundAmount();
        }, 0);

        $this->setRefundAmount($sum);
    }

    /**
     * @param int $id
     */
    public function setDerivedProperty($id)
    {
        if (!$this->refundNumber) {
            $this->setRefundNumber(sprintf('%09d', $id));
        }
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
    public function getRefundNumber()
    {
        return $this->refundNumber;
    }

    /**
     * @param string $refundNumber
     *
     * @return Refund
     */
    public function setRefundNumber($refundNumber)
    {
        $this->refundNumber = $refundNumber;

        return $this;
    }

    /**
     * @return float
     */
    public function getRefundAmount()
    {
        return $this->refundAmount;
    }

    /**
     * @param int $refundAmount
     *
     * @return Refund
     */
    public function setRefundAmount($refundAmount)
    {
        $this->refundAmount = $refundAmount;

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
     *
     * @return Refund
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return Collection|RefundItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param Collection|RefundItem[] $items
     *
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @param RefundItem $item
     *
     * @return $this
     */
    public function addItem(RefundItem $item)
    {
        $this->items->add($item->setRefund($this));

        return $this;
    }

    /**
     * @param RefundItem $item
     *
     * @return $this
     */
    public function removeItem(RefundItem $item)
    {
        $this->items->removeElement($item);

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
     *
     * @return $this
     */
    public function setOrder($order)
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
     *
     * @return Refund
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return float
     */
    public function getRefundSubtotal()
    {
        return $this->refundSubtotal;
    }

    /**
     * @param float $refundSubtotal
     * @return $this
     */
    public function setRefundSubtotal($refundSubtotal)
    {
        $this->refundSubtotal = $refundSubtotal;

        return $this;
    }

    /**
     * @return float
     */
    public function getRefundTaxTotal()
    {
        return $this->refundTaxTotal;
    }

    /**
     * @param $refundTaxTotal
     * @return $this
     */
    public function setRefundTaxTotal($refundTaxTotal)
    {
        $this->refundTaxTotal = $refundTaxTotal;

        return $this;
    }
}
