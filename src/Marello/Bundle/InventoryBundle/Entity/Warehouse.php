<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Oro\Bundle\EmailBundle\Model\EmailHolderInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;

#[ORM\Table(name: 'marello_inventory_warehouse')]
#[ORM\UniqueConstraint(columns: ['code'])]
#[ORM\Entity(repositoryClass: \Marello\Bundle\InventoryBundle\Entity\Repository\WarehouseRepository::class)]
#[Oro\Config(defaultValues: ['security' => ['type' => 'ACL', 'group_name' => ''], 'ownership' => ['owner_type' => 'ORGANIZATION', 'owner_field_name' => 'owner', 'owner_column_name' => 'owner_id'], 'dataaudit' => ['auditable' => true]])]
class Warehouse implements EmailHolderInterface, ExtendEntityInterface
{
    use ExtendEntityTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     *
     * @var string
     */
    #[ORM\Column(name: 'label', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $label;

    /**
     *
     * @var string
     */
    #[ORM\Column(name: 'code', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $code;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'is_default', type: Types::BOOLEAN, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true], 'dataaudit' => ['auditable' => true]])]
    protected $default;

    /**
     * @var OrganizationInterface
     */
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Oro\Bundle\OrganizationBundle\Entity\Organization::class)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true], 'dataaudit' => ['auditable' => true]])]
    protected $owner;

    /**
     * @var MarelloAddress
     */
    #[ORM\JoinColumn(nullable: true)]
    #[ORM\OneToOne(targetEntity: \Marello\Bundle\AddressBundle\Entity\MarelloAddress::class, cascade: ['persist', 'remove'])]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 40, 'full' => true], 'dataaudit' => ['auditable' => true]])]
    protected $address = null;

    /**
     * @var WarehouseType
     */
    #[ORM\JoinColumn(name: 'warehouse_type', referencedColumnName: 'name')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\InventoryBundle\Entity\WarehouseType::class)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 50, 'full' => true], 'dataaudit' => ['auditable' => true]])]
    protected $warehouseType;
    
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \WarehouseGroup::class, inversedBy: 'warehouses')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['order' => 60, 'full' => true], 'dataaudit' => ['auditable' => true]])]
    protected $group;
    
    /**
     * @var string
     */
    #[ORM\Column(name: 'email', type: Types::STRING, nullable: true)]
    protected $email;

    /**
     * @var string
     */
    #[ORM\Column(name: 'notifier', type: Types::STRING, nullable: true)]
    protected $notifier;

    /**
     * @var int
     */
    #[ORM\Column(name: 'sort_order_ood_loc', type: Types::INTEGER, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'entity' => ['label' => 'marello.inventory.warehouse.sort_order_ood_loc.label']])]
    protected $sortOrderOodLoc;

    /**
     * @var bool
     */
    #[ORM\Column(name: 'order_on_demand_location', type: Types::BOOLEAN, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $orderOnDemandLocation;

    /**
     * @param string $label
     * @param bool   $default
     */
    public function __construct($label = null, $default = false)
    {
        $this->label   = $label;
        $this->default = $default;
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
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param boolean $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return OrganizationInterface
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param OrganizationInterface $owner
     *
     * @return $this
     */
    public function setOwner(OrganizationInterface $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->label;
    }

    /**
     * @return MarelloAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param MarelloAddress $address
     *
     * @return $this
     */
    public function setAddress(MarelloAddress $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return WarehouseType
     */
    public function getWarehouseType()
    {
        return $this->warehouseType;
    }

    /**
     * @param WarehouseType $warehouseType
     *
     * @return $this
     */
    public function setWarehouseType(WarehouseType $warehouseType)
    {
        $this->warehouseType = $warehouseType;

        return $this;
    }

    /**
     * @return WarehouseGroup|null
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param WarehouseGroup|null $group
     *
     * @return $this
     */
    public function setGroup(WarehouseGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Warehouse
     */
    public function setEmail($email = null): self
    {
        $this->email = $email;
        
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNotifier(): ?string
    {
        return $this->notifier;
    }

    /**
     * @param string|null $notifier
     * @return $this
     */
    public function setNotifier(string $notifier = null): self
    {
        $this->notifier = $notifier;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSortOrderOodLoc(): ?int
    {
        return $this->sortOrderOodLoc;
    }

    /**
     * @param int|null $sortOrderOodLoc
     * @return $this
     */
    public function setSortOrderOodLoc(int $sortOrderOodLoc = null): self
    {
        $this->sortOrderOodLoc = $sortOrderOodLoc;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isOrderOnDemandLocation(): ?bool
    {
        return $this->orderOnDemandLocation;
    }

    /**
     * @param bool|null $orderOnDemandLocation
     * @return $this
     */
    public function setOrderOnDemandLocation(bool $orderOnDemandLocation = null): self
    {
        $this->orderOnDemandLocation = $orderOnDemandLocation;

        return $this;
    }
}
