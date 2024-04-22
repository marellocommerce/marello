<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\OrderBundle\Model\QuantityAwareInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;

/**
 * @Oro\Config(
 *      defaultValues={
 *          "dataaudit"={
 *              "auditable"=true
 *          },
 *          "ownership"={
 *              "owner_type"="ORGANIZATION",
 *              "owner_field_name"="organization",
 *              "owner_column_name"="organization_id"
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_inventory_alloc_item')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class AllocationItem implements QuantityAwareInterface, OrganizationAwareInterface, ExtendEntityInterface
{
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
     * @var Allocation
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'allocation_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\InventoryBundle\Entity\Allocation::class, inversedBy: 'items')]
    protected $allocation;

    /**
     * @var Product
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'SET NULL', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\ProductBundle\Entity\Product::class)]
    protected $product;

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
    #[ORM\Column(name: 'product_sku', type: 'string', nullable: false)]
    protected $productSku;

    /**
     * @var string
     */
    #[ORM\Column(name: 'product_name', type: 'string', nullable: false)]
    protected $productName;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'order_item_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\OrderBundle\Entity\OrderItem::class)]
    protected $orderItem;

    /**
     * @var float
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'quantity', type: 'float', nullable: false)]
    protected $quantity;

    /**
     * @var float
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'total_quantity', type: 'float', nullable: true)]
    protected $totalQuantity;

    /**
     * @var float
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'quantity_confirmed', type: 'float', nullable: true)]
    protected $quantityConfirmed;

    /**
     * @var float
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'quantity_rejected', type: 'float', nullable: true)]
    protected $quantityRejected;

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
    #[ORM\Column(name: 'comment', type: 'text', nullable: true)]
    protected $comment;

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

    #[ORM\PrePersist]
    public function prePersist()
    {
        // prevent overriding product name if already being set
        if (is_null($this->productName)) {
            $this->setProductName((string)$this->product->getName());
        }
        if (is_null($this->productSku)) {
            $this->setProductSku($this->product->getSku());
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
     * @return Allocation
     */
    public function getAllocation()
    {
        return $this->allocation;
    }

    /**
     * @param Allocation $allocation
     *
     * @return $this
     */
    public function setAllocation(Allocation $allocation)
    {
        $this->allocation = $allocation;
        
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->productSku;
    }

    /**
     * @param string $productSku
     *
     * @return $this
     */
    public function setProductSku($productSku)
    {
        $this->productSku = $productSku;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     *
     * @return $this
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param mixed $orderItem
     *
     * @return $this
     */
    public function setOrderItem($orderItem)
    {
        $this->orderItem = $orderItem;

        return $this;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalQuantity()
    {
        return $this->totalQuantity;
    }

    /**
     * @param float $totalQuantity
     *
     * @return $this
     */
    public function setTotalQuantity($totalQuantity)
    {
        $this->totalQuantity = $totalQuantity;

        return $this;
    }

    /**
     * @return float
     */
    public function getQuantityConfirmed()
    {
        return $this->quantityConfirmed;
    }

    /**
     * @param float $quantityConfirmed
     *
     * @return $this
     */
    public function setQuantityConfirmed($quantityConfirmed)
    {
        $this->quantityConfirmed = $quantityConfirmed;

        return $this;
    }

    /**
     * @return float
     */
    public function getQuantityRejected()
    {
        return $this->quantityRejected;
    }

    /**
     * @param float $quantityRejected
     *
     * @return $this
     */
    public function setQuantityRejected($quantityRejected)
    {
        $this->quantityRejected = $quantityRejected;

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
}
