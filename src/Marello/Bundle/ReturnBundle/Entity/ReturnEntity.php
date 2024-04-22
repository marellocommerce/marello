<?php

namespace Marello\Bundle\ReturnBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CoreBundle\DerivedProperty\DerivedPropertyAwareInterface;
use Marello\Bundle\LocaleBundle\Model\LocalizationAwareInterface;
use Marello\Bundle\LocaleBundle\Model\LocalizationTrait;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Entity\OrderAwareInterface;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\ShippingBundle\Entity\HasShipmentTrait;
use Marello\Bundle\SalesBundle\Model\SalesChannelAwareInterface;
use Marello\Bundle\ShippingBundle\Integration\ShippingAwareInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

/**
 * @Oro\Config(
 *      routeView="marello_return_return_view",
 *      routeName="marello_return_return_index",
 *      routeCreate="marello_return_return_create",
 *      defaultValues={
 *          "entity"={
 *              "icon"="fa-undo"
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
 *          "grid"={
 *              "default"="marello-return-select-grid"
 *          },
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_return_return')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\ReturnBundle\Entity\Repository\ReturnEntityRepository::class)]
#[ORM\HasLifecycleCallbacks]
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
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Order
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\OrderBundle\Entity\Order::class)]
    protected $order;

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
    #[ORM\Column(name: 'return_number', type: 'string', nullable: true)]
    protected $returnNumber;

    /**
     * @var Collection|ReturnItem[]
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
    #[ORM\JoinColumn]
    #[ORM\OneToMany(targetEntity: \Marello\Bundle\ReturnBundle\Entity\ReturnItem::class, mappedBy: 'return', cascade: ['persist', 'remove'], orphanRemoval: true)]
    protected $returnItems;

    /**
     * @var SalesChannel
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'sales_channel_id', onDelete: 'SET NULL', nullable: true)]
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
    #[ORM\Column(name: 'sales_channel_name', type: 'string', nullable: false)]
    protected $salesChannelName;

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
    #[ORM\Column(name: 'return_reference', type: 'string', nullable: true)]
    protected $returnReference;

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
    #[ORM\Column(name: 'received_at', type: 'datetime', nullable: true)]
    protected $receivedAt;

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
    #[ORM\Column(name: 'track_trace_code', type: 'string', nullable: true)]
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
