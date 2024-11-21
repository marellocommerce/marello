<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\CoreBundle\Model\HashAwareTrait;
use Marello\Bundle\CoreBundle\Model\HashAwareInterface;
use Marello\Bundle\OrderBundle\Model\QuantityAwareInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;

#[ORM\Table(name: 'marello_inventory_alloc_item')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(
    defaultValues: [
        'dataaudit' => ['auditable' => true],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id']
    ]
)]
class AllocationItem implements QuantityAwareInterface,
    OrganizationAwareInterface,
    ExtendEntityInterface,
    HashAwareInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;
    use HashAwareTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Allocation
     */
    #[ORM\JoinColumn(name: 'allocation_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Allocation::class, inversedBy: 'items')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $allocation;

    /**
     * @var Product
     */
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $product;

    /**
     * @var string
     */
    #[ORM\Column(name: 'product_sku', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $productSku;

    /**
     * @var string
     */
    #[ORM\Column(name: 'product_name', type: Types::STRING, nullable: false)]
    protected $productName;

    #[ORM\JoinColumn(name: 'order_item_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: OrderItem::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $orderItem;

    /**
     * @var float
     */
    #[ORM\Column(name: 'quantity', type: Types::FLOAT, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $quantity;

    /**
     * @var float
     */
    #[ORM\Column(name: 'total_quantity', type: Types::FLOAT, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $totalQuantity;

    /**
     * @var float
     */
    #[ORM\Column(name: 'quantity_confirmed', type: Types::FLOAT, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $quantityConfirmed;

    /**
     * @var float
     */
    #[ORM\Column(name: 'quantity_rejected', type: Types::FLOAT, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $quantityRejected;

    /**
     * @var string
     */
    #[ORM\Column(name: 'comment', type: Types::TEXT, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $comment;

    /**
     * @var Warehouse
     */
    #[ORM\JoinColumn(name: 'warehouse_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: Warehouse::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $warehouse;

    #[ORM\Column(name: 'inventory_batches', type: Types::JSON, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $inventoryBatches = [];

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

    /**
     * @return array|null
     */
    public function getInventoryBatches(): ?array
    {
        return $this->inventoryBatches;
    }

    /**
     * @param array|null $batches
     * @return $this
     */
    public function setInventoryBatches(array $batches = null): self
    {
        $this->inventoryBatches = $batches;

        return $this;
    }
}
