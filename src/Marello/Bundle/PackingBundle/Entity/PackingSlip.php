<?php

namespace Marello\Bundle\PackingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\InventoryBundle\Entity\Allocation;
use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\SalesBundle\Model\SalesChannelAwareInterface;
use Marello\Bundle\CoreBundle\DerivedProperty\DerivedPropertyAwareInterface;

#[ORM\Table(name: 'marello_packing_packing_slip')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(
    defaultValues: [
        'entity' => ['icon' => 'fa-list-alt'],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true]
    ]
)]
class PackingSlip implements
    DerivedPropertyAwareInterface,
    OrganizationAwareInterface,
    SalesChannelAwareInterface,
    ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;
    
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Collection|PackingSlipItem[]
     */
    #[ORM\OneToMany(
        mappedBy: 'packingSlip',
        targetEntity: PackingSlipItem::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $items;

    /**
     * @var Order
     */
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Order::class, cascade: ['persist'])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $order;

    /**
     * @var AbstractAddress
     */
    #[ORM\JoinColumn(name: 'billing_address_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: MarelloAddress::class, cascade: ['persist'])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $billingAddress;

    /**
     * @var AbstractAddress
     */
    #[ORM\JoinColumn(name: 'shipping_address_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: MarelloAddress::class, cascade: ['persist'])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $shippingAddress;

    /**
     * @var Customer
     */
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Customer::class, cascade: ['persist'])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $customer;

    /**
     * @var SalesChannel
     */
    #[ORM\JoinColumn(onDelete: 'SET NULL', nullable: true)]
    #[ORM\ManyToOne(targetEntity: SalesChannel::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $salesChannel;

    /**
     * @var string
     */
    #[ORM\Column(name: 'saleschannel_name', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $salesChannelName;

    /**
     * @var Warehouse
     */
    #[ORM\JoinColumn(onDelete: 'SET NULL', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Warehouse::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $warehouse;

    /**
     * @var string
     */
    #[ORM\Column(name: 'comment', type: Types::TEXT, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $comment;

    /**
     * @var string
     */
    #[ORM\Column(name: 'packing_slip_number', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $packingSlipNumber;

    /**
     * @var Allocation
     */
    #[ORM\JoinColumn(name: 'source_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: Allocation::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => false]])]
    protected $sourceEntity;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|PackingSlipItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param PackingSlipItem $item
     *
     * @return $this
     */
    public function addItem(PackingSlipItem $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setPackingSlip($this);
        }

        return $this;
    }

    /**
     * @param PackingSlipItem $item
     *
     * @return $this
     */
    public function removeItem(PackingSlipItem $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }

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
    public function setOrder(Order $order)
    {
        $this->order = $order;

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
     *
     * @return $this
     */
    public function setShippingAddress(MarelloAddress $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;

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
     *
     * @return $this
     */
    public function setBillingAddress(MarelloAddress $billingAddress)
    {
        $this->billingAddress = $billingAddress;

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
     * @return $this
     */
    public function setCustomer($customer)
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
     *
     * @return $this
     */
    public function setSalesChannel($salesChannel)
    {
        $this->salesChannel = $salesChannel;
        if ($this->salesChannel) {
            $this->salesChannelName = $this->salesChannel->getName();
        }

        return $this;
    }

    /**
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @param Warehouse $warehouse
     *
     * @return $this
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return $this
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPackingSlipNumber()
    {
        return $this->packingSlipNumber;
    }

    /**
     * @param $packingSlipNumber
     * @return $this
     */
    public function setPackingSlipNumber($packingSlipNumber)
    {
        $this->packingSlipNumber = $packingSlipNumber;

        return $this;
    }

    /**
     * @return Allocation|null
     */
    public function getSourceEntity(): ?Allocation
    {
        return $this->sourceEntity;
    }

    /**
     * @param Allocation|null $sourceEntity
     */
    public function setSourceEntity(Allocation $sourceEntity = null): self
    {
        $this->sourceEntity = $sourceEntity;

        return $this;
    }

    /**
     * @param $id
     */
    public function setDerivedProperty($id)
    {
        if (!$this->packingSlipNumber) {
            $this->setPackingSlipNumber(sprintf('%09d', $id));
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('#%s', $this->packingSlipNumber);
    }
}
