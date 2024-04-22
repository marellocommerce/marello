<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\AbstractEnumValue;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Marello\Bundle\ShippingBundle\Entity\HasShipmentTrait;
use Marello\Bundle\OrderBundle\Entity\OrderAwareInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\ShippingBundle\Integration\ShippingAwareInterface;
use Marello\Bundle\CoreBundle\DerivedProperty\DerivedPropertyAwareInterface;

/**
 * @Oro\Config(
 *      routeView="marello_inventory_allocation_view",
 *      routeName="marello_inventory_allocation_index",
 *      defaultValues={
 *          "entity"={
 *              "icon"="fa-list-alt"
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
 *          },
 *          "grid"={
 *              "context"="marello-allocation-for-context-grid"
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_inventory_allocation')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Allocation implements
    DerivedPropertyAwareInterface,
    OrganizationAwareInterface,
    OrderAwareInterface,
    ShippingAwareInterface,
    ExtendEntityInterface
{
    use HasShipmentTrait;
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;
    
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    protected $id;

    /**
     * @var Collection|AllocationItem[]
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
     *
     */
    #[ORM\OneToMany(targetEntity: \Marello\Bundle\InventoryBundle\Entity\AllocationItem::class, mappedBy: 'allocation', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $items;

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
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\OrderBundle\Entity\Order::class, cascade: ['persist'])]
    protected $order;

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
     * @var Warehouse
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'warehouse_id', referencedColumnName: 'id', onDelete: 'SET NULL', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\InventoryBundle\Entity\Warehouse::class)]
    protected $warehouse;

    /**
     * @var Allocation
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\InventoryBundle\Entity\Allocation::class, inversedBy: 'children')]
    protected $parent;

    /**
     * @var Collection|Allocation[]
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\OneToMany(targetEntity: \Marello\Bundle\InventoryBundle\Entity\Allocation::class, mappedBy: 'parent')]
    protected $children;

    /**
     * @var Allocation
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'source_entity_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\InventoryBundle\Entity\Allocation::class)]
    protected $sourceEntity;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'allocation_number', type: 'string', nullable: true)]
    protected $allocationNumber;

    /**
     * @var \Extend\Entity\EV_Marello_Allocation_State
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $state;

    /**
     * @var \Extend\Entity\EV_Marello_Allocation_Status
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $status;

    /**
     * @var \Extend\Entity\EV_Marello_Allocation_AllocationContext
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $allocationContext;

    /**
     * @var \Extend\Entity\EV_Marello_Allocation_ReshipmentReason
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    protected $reshipmentReason;

    /**
     * Allocation constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|AllocationItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param AllocationItem $item
     *
     * @return $this
     */
    public function addItem(AllocationItem $item)
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setAllocation($this);
        }

        return $this;
    }

    /**
     * @param AllocationItem $item
     *
     * @return $this
     */
    public function removeItem(AllocationItem $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);
        }

        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     *
     * @return $this
     */
    public function setOrder(Order $order): self
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
     * @return Warehouse|null
     */
    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    /**
     * @param Warehouse|null $warehouse
     * @return $this
     */
    public function setWarehouse(Warehouse $warehouse = null): self
    {
        $this->warehouse = $warehouse;
        
        return $this;
    }

    /**
     * @param Allocation|null $parent
     * @return $this
     */
    public function setParent(Allocation $parent = null): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Allocation
     */
    public function getParent(): ?Allocation
    {
        return $this->parent;
    }

    /**
     * @param Allocation $child
     * @return $this
     */
    public function addChild(Allocation $child): self
    {
        if (!$this->hasChild($child)) {
            $child->setParent($this);
            $this->children->add($child);
        }

        return $this;
    }

    /**
     * @param Allocation $child
     *
     * @return $this
     */
    public function removeChild(Allocation $child): self
    {
        if ($this->hasChild($child)) {
            $child->setParent(null);
            $this->children->removeElement($child);
        }

        return $this;
    }

    /**
     * @return Collection|Allocation[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return ($this->children->count() > 0);
    }

    /**
     * @param Allocation $child
     *
     * @return bool
     */
    public function hasChild(Allocation $child)
    {
        return $this->children->contains($child);
    }

    /**
     * @return Allocation|null
     */
    public function getSourceEntity(): ?Allocation
    {
        return $this->sourceEntity;
    }

    /**
     * @param Allocation $sourceEntity
     * @return $this
     */
    public function setSourceEntity(Allocation $sourceEntity = null): self
    {
        $this->sourceEntity = $sourceEntity;

        return $this;
    }

    /**
     * @return \Extend\Entity\EV_Marello_Allocation_State
     */
    public function getState(): ?AbstractEnumValue
    {
        return $this->state;
    }

    /**
     * @param \Extend\Entity\EV_Marello_Allocation_State $state
     * @return $this
     */
    public function setState($state): self
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return \Extend\Entity\EV_Marello_Allocation_Status
     */
    public function getStatus(): ?\Extend\Entity\EV_Marello_Allocation_Status
    {
        return $this->status;
    }

    /**
     * @param \Extend\Entity\EV_Marello_Allocation_Status $status
     * @return $this
     */
    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \Extend\Entity\EV_Marello_Allocation_AllocationContext
     */
    public function getAllocationContext(): ?AbstractEnumValue
    {
        return $this->allocationContext;
    }

    /**
     * @param \Extend\Entity\EV_Marello_Allocation_AllocationContext $allocationContext
     * @return $this
     */
    public function setAllocationContext($allocationContext): self
    {
        $this->allocationContext = $allocationContext;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAllocationNumber()
    {
        return $this->allocationNumber;
    }

    /**
     * @param $allocationNumber
     * @return $this
     */
    public function setAllocationNumber($allocationNumber)
    {
        $this->allocationNumber = $allocationNumber;

        return $this;
    }

    /**
     * @param $id
     */
    public function setDerivedProperty($id)
    {
        if (!$this->allocationNumber) {
            $this->setAllocationNumber(sprintf('%09d', $id));
        }
    }

    /**
     * @return \Extend\Entity\EV_Marello_Allocation_ReshipmentReason
     */
    public function getReshipmentReason(): ?AbstractEnumValue
    {
        return $this->reshipmentReason;
    }

    /**
     * @param \Extend\Entity\EV_Marello_Allocation_ReshipmentReason $reshipmentReason
     * @return $this
     */
    public function setReshipmentReason($reshipmentReason): self
    {
        $this->reshipmentReason = $reshipmentReason;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('#%s', $this->allocationNumber);
    }
}
