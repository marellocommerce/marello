<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\InventoryBundle\Model\BalancedInventoryLevelInterface;
use Marello\Bundle\ProductBundle\Entity\ProductInterface;
use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

/**
 * @Oro\Config(
 *      defaultValues={
 *          "dataaudit"={
 *              "auditable"=false
 *          },
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
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_blncd_inventory_level')]
#[ORM\UniqueConstraint(columns: ['product_id', 'channel_group_id'])]
#[ORM\Entity(repositoryClass: \Marello\Bundle\InventoryBundle\Entity\Repository\BalancedInventoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class BalancedInventoryLevel implements OrganizationAwareInterface, BalancedInventoryLevelInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    protected $id;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="marello.product.entity_label"
     *          },
     *          "dataaudit"={
     *              "auditable"=false
     *          }
     *      }
     * )
     *
     * @var ProductInterface
     */
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\ProductBundle\Entity\Product::class)]
    protected $product;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="marello.sales.saleschannelgroup.entity_label"
     *          },
     *          "dataaudit"={
     *              "auditable"=false
     *          }
     *      }
     * )
     *
     * @var SalesChannelGroup
     */
    #[ORM\JoinColumn(name: 'channel_group_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\SalesBundle\Entity\SalesChannelGroup::class)]
    protected $salesChannelGroup;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="marello.inventory.balancedinventorylevel.inventory.label"
     *          },
     *          "dataaudit"={
     *              "auditable"=false
     *          }
     *      }
     * )
     * @var int
     */
    #[ORM\Column(name: 'inventory_qty', type: 'integer', nullable: false)]
    protected $inventory;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="marello.inventory.balancedinventorylevel.balanced_inventory_qty.label"
     *          },
     *          "dataaudit"={
     *              "auditable"=false
     *          }
     *      }
     * )
     * @var int
     */
    #[ORM\Column(name: 'blncd_inventory_qty', type: 'integer', nullable: false)]
    protected $balancedInventory;


    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="marello.inventory.balancedinventorylevel.reserved_inventory_qty.label"
     *          },
     *          "dataaudit"={
     *              "auditable"=false
     *          }
     *      }
     * )
     * @var int
     */
    #[ORM\Column(name: 'reserved_inventory_qty', type: 'integer', nullable: true)]
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
