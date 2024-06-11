<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;
use Marello\Bundle\ProductBundle\Entity\ProductInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\InventoryBundle\Model\BalancedInventoryLevelInterface;
use Marello\Bundle\InventoryBundle\Entity\Repository\BalancedInventoryRepository;

#[ORM\Table(name: 'marello_blncd_inventory_level')]
#[ORM\UniqueConstraint(columns: ['product_id', 'channel_group_id'])]
#[ORM\Entity(repositoryClass: BalancedInventoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(
    defaultValues: [
        'dataaudit' => ['auditable' => false],
        'entity' => ['icon' => 'fa-list-alt'],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ]
    ]
)]
class BalancedInventoryLevel implements OrganizationAwareInterface, BalancedInventoryLevelInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected $id;

    /**
     * @var ProductInterface
     */
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[Oro\ConfigField(
        defaultValues: [
            'entity' => ['label' => 'marello.product.entity_label'],
            'dataaudit' => ['auditable' => false]
        ]
    )]
    protected $product;

    /**
     * @var SalesChannelGroup
     */
    #[ORM\JoinColumn(name: 'channel_group_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: SalesChannelGroup::class)]
    #[Oro\ConfigField(
        defaultValues: [
            'entity' => ['label' => 'marello.sales.saleschannelgroup.entity_label'],
            'dataaudit' => ['auditable' => false]
        ]
    )]
    protected $salesChannelGroup;

    /**
     * @var int
     */
    #[ORM\Column(name: 'inventory_qty', type: Types::INTEGER, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'entity' => ['label' => 'marello.inventory.balancedinventorylevel.inventory.label'],
            'dataaudit' => ['auditable' => false]
        ]
    )]
    protected $inventory;

    /**
     * @var int
     */
    #[ORM\Column(name: 'blncd_inventory_qty', type: Types::INTEGER, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'entity' => ['label' => 'marello.inventory.balancedinventorylevel.balanced_inventory_qty.label'],
            'dataaudit' => ['auditable' => false]
        ]
    )]
    protected $balancedInventory;

    /**
     * @var int
     */
    #[ORM\Column(name: 'reserved_inventory_qty', type: Types::INTEGER, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'entity' => ['label' => 'marello.inventory.balancedinventorylevel.reserved_inventory_qty.label'],
            'dataaudit' => ['auditable' => false]
        ]
    )]
    protected $reservedInventory;

    /**
     * BalancedInventoryLevel constructor.
     * @param ProductInterface $product
     * @param SalesChannelGroup $group
     * @param null $inventory
     */
    public function __construct(ProductInterface $product, SalesChannelGroup $group, $inventory = null)
    {
        $this->product = $product;
        $this->salesChannelGroup = $group;
        $this->inventory = $this->balancedInventory = $inventory;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param ProductInterface $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return SalesChannelGroup
     */
    public function getSalesChannelGroup()
    {
        return $this->salesChannelGroup;
    }

    /**
     * @param SalesChannelGroup $salesChannelGroup
     * @return $this
     */
    public function setSalesChannelGroup(SalesChannelGroup $salesChannelGroup)
    {
        $this->salesChannelGroup = $salesChannelGroup;

        return $this;
    }

    /**
     * @return int
     */
    public function getInventoryQty()
    {
        return $this->inventory;
    }

    /**
     * @param int $inventory
     * @return $this
     */
    public function setInventoryQty($inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * @return int
     */
    public function getReservedInventoryQty()
    {
        return $this->reservedInventory;
    }

    /**
     * @param int $reservedInventory
     * @return $this
     */
    public function setReservedInventoryQty($reservedInventory)
    {
        $this->reservedInventory = $reservedInventory;

        return $this;
    }

    /**
     * @return int
     */
    public function getBalancedInventoryQty()
    {
        return $this->balancedInventory;
    }

    /**
     * @param $balancedInventory
     * @return $this
     */
    public function setBalancedInventoryQty($balancedInventory)
    {
        $this->balancedInventory = $balancedInventory;

        return $this;
    }
}
