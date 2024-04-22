<?php

namespace Marello\Bundle\PurchaseOrderBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CoreBundle\DerivedProperty\DerivedPropertyAwareInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

/**
 * @Oro\Config(
 *      routeView="marello_purchaseorder_purchaseorder_view",
 *      routeName="marello_purchaseorder_purchaseorder_index",
 *      routeCreate="marello_purchaseorder_purchaseorder_create",
 *      defaultValues={
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
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_purchase_order')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class PurchaseOrder implements DerivedPropertyAwareInterface, ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;

    /**
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

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
    #[ORM\Column(name: 'purchase_order_number', type: 'string', nullable: true)]
    protected $purchaseOrderNumber;

    /**
     * @var Collection|PurchaseOrderItem[]
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
    #[ORM\OneToMany(targetEntity: \PurchaseOrderItem::class, mappedBy: 'order', cascade: ['persist'], orphanRemoval: true)]
    protected $items;

    /**
     * @var Supplier
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'supplier_id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\SupplierBundle\Entity\Supplier::class)]
    protected $supplier;

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
    #[ORM\Column(name: 'due_date', type: 'datetime', nullable: true)]
    protected $dueDate;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var Warehouse
     */
    #[ORM\JoinColumn(name: 'warehouse_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\InventoryBundle\Entity\Warehouse::class)]
    protected $warehouse;

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
    #[ORM\Column(name: 'order_total', type: 'money', nullable: false)]
    protected $orderTotal;

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
    #[ORM\Column(name: 'purchase_order_reference', type: 'string', nullable: true)]
    protected $purchaseOrderReference;

    /**
     * @var array $data
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'data', type: 'json_array', nullable: true)]
    protected $data = [];

    /**
     * PurchaseOrder constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getPurchaseOrderNumber(): ?string
    {
        return $this->purchaseOrderNumber;
    }

    /**
     * @return Collection
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * @param $purchaseOrderNumber
     * @return $this
     */
    public function setPurchaseOrderNumber(string $purchaseOrderNumber): self
    {
        $this->purchaseOrderNumber = $purchaseOrderNumber;

        return $this;
    }

    /**
     * @param PurchaseOrderItem $item
     * @return $this
     */
    public function addItem(PurchaseOrderItem $item): self
    {
        $this->items->add($item->setOrder($this));

        return $this;
    }

    /**
     * @param PurchaseOrderItem $item
     * @return $this
     */
    public function removeItem(PurchaseOrderItem $item): self
    {
        $this->items->removeElement($item);

        return $this;
    }

    /**
     * @return Supplier|null
     */
    public function getSupplier(): ?Supplier
    {
        return $this->supplier;
    }

    /**
     * @param Supplier $supplier
     * @return $this
     */
    public function setSupplier(Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    /**
     * @param $id
     */
    public function setDerivedProperty($id): void
    {
        if (!$this->purchaseOrderNumber) {
            $this->setPurchaseOrderNumber(sprintf('%09d', $id));
        }
    }

    /**
     * @param \DateTime|null $dueDate
     * @return $this
     */
    public function setDueDate(\DateTime $dueDate = null): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    /**
     * @return Warehouse|null
     */
    public function getWarehouse(): ?Warehouse
    {
        return $this->warehouse;
    }

    /**
     * @param Warehouse $warehouse
     * @return $this
     */
    public function setWarehouse(Warehouse $warehouse): self
    {
        $this->warehouse = $warehouse;

        return $this;
    }

    /**
     * @return float
     */
    public function getOrderTotal(): float
    {
        return $this->orderTotal;
    }

    /**
     * @param float $orderTotal
     * @return $this
     */
    public function setOrderTotal(float $orderTotal): self
    {
        $this->orderTotal = $orderTotal;
        
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPurchaseOrderReference(): ?string
    {
        return $this->purchaseOrderReference;
    }

    /**
     * @param string|null $purchaseOrderReference
     * @return $this
     */
    public function setPurchaseOrderReference(string $purchaseOrderReference = null): self
    {
        $this->purchaseOrderReference = $purchaseOrderReference;

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('#%s', $this->purchaseOrderNumber);
    }
}
