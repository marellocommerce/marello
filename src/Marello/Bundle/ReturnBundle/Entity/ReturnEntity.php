<?php

namespace Marello\Bundle\ReturnBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\LocaleBundle\Model\LocalizationTrait;
use Marello\Bundle\ShippingBundle\Entity\HasShipmentTrait;
use Marello\Bundle\OrderBundle\Entity\OrderAwareInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\SalesBundle\Model\SalesChannelAwareInterface;
use Marello\Bundle\LocaleBundle\Model\LocalizationAwareInterface;
use Marello\Bundle\ShippingBundle\Integration\ShippingAwareInterface;
use Marello\Bundle\ReturnBundle\Entity\Repository\ReturnEntityRepository;
use Marello\Bundle\CoreBundle\DerivedProperty\DerivedPropertyAwareInterface;

#[ORM\Table(name: 'marello_return_return')]
#[ORM\Entity(repositoryClass: ReturnEntityRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(
    routeView: 'marello_return_return_view',
    routeName: 'marello_return_return_index',
    routeCreate: 'marello_return_return_create',
    defaultValues: [
        'entity' => ['icon' => 'fa-undo'],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'grid' => ['default' => 'marello-return-select-grid'],
        'dataaudit' => ['auditable' => true]
    ]
)]
class ReturnEntity implements
    DerivedPropertyAwareInterface,
    ShippingAwareInterface,
    LocalizationAwareInterface,
    OrganizationAwareInterface,
    OrderAwareInterface,
    SalesChannelAwareInterface,
    ExtendEntityInterface
{
    use HasShipmentTrait;
    use LocalizationTrait;
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
     * @var Order
     */
    #[ORM\ManyToOne(targetEntity: Order::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $order;

    /**
     * @var string
     */
    #[ORM\Column(name: 'return_number', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $returnNumber;

    /**
     * @var Collection|ReturnItem[]
     */
    #[ORM\JoinColumn]
    #[ORM\OneToMany(
        mappedBy: 'return',
        targetEntity: ReturnItem::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    #[Oro\ConfigField(
        defaultValues: ['email' => ['available_in_template' => true], 'dataaudit' => ['auditable' => true]]
    )]
    protected $returnItems;

    /**
     * @var SalesChannel
     */
    #[ORM\JoinColumn(name: 'sales_channel_id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: SalesChannel::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $salesChannel;

    /**
     * @var string
     */
    #[ORM\Column(name: 'sales_channel_name', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $salesChannelName;

    /**
     * @var string
     */
    #[ORM\Column(name: 'return_reference', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $returnReference;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'received_at', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $receivedAt;

    /**
     * @var string
     */
    #[ORM\Column(name: 'track_trace_code', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $trackTraceCode;

    /**
     * ReturnEntity constructor.
     */
    public function __construct()
    {
        $this->returnItems = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
        $this->organization = $order->getOrganization();
        $this->localization = $order->getLocalization();

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnNumber()
    {
        return $this->returnNumber;
    }

    /**
     * @param string $returnNumber
     *
     * @return $this
     */
    public function setReturnNumber($returnNumber)
    {
        $this->returnNumber = $returnNumber;

        return $this;
    }

    /**
     * @return Collection|ReturnItem[]
     */
    public function getReturnItems()
    {
        return $this->returnItems;
    }

    /**
     * @param ReturnItem $item
     *
     * @return $this
     */
    public function addReturnItem(ReturnItem $item)
    {
        $this->returnItems->add($item->setReturn($this));

        return $this;
    }

    /**
     * @param ReturnItem $item
     *
     * @return $this
     */
    public function removeReturnItem(ReturnItem $item)
    {
        $this->returnItems->removeElement($item);

        return $this;
    }

    /**
     * @param $id
     */
    public function setDerivedProperty($id)
    {
        if (!$this->returnNumber) {
            $this->setReturnNumber(sprintf('%09d', $id));
        }
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
    public function setSalesChannel($salesChannel = null)
    {
        $this->salesChannel = $salesChannel;
        if ($this->salesChannel) {
            $this->salesChannelName = $this->salesChannel->getName();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSalesChannelName()
    {
        return $this->salesChannelName;
    }

    public function __toString()
    {
        return (string) $this->getReturnNumber();
    }

    /**
     * @return string
     */
    public function getReturnReference()
    {
        return $this->returnReference;
    }

    /**
     * @param string $returnReference
     *
     * @return $this
     */
    public function setReturnReference($returnReference)
    {
        $this->returnReference = $returnReference;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReceivedAt(): ?\DateTime
    {
        return $this->receivedAt;
    }

    /**
     * @param \DateTime $receivedAt
     * @return ReturnEntity
     */
    public function setReceivedAt(?\DateTime $receivedAt): ReturnEntity
    {
        $this->receivedAt = $receivedAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getTrackTraceCode(): ?string
    {
        return $this->trackTraceCode;
    }

    /**
     * @param string $trackTraceCode
     * @return ReturnEntity
     */
    public function setTrackTraceCode(string $trackTraceCode): ReturnEntity
    {
        $this->trackTraceCode = $trackTraceCode;

        return $this;
    }
}
