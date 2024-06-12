<?php

namespace Marello\Bundle\ProductBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Marello\Bundle\ProductBundle\Entity\Repository\ProductSupplierRelationRepository;

/**
 * ProductSupplierRelation
 */
#[ORM\Table(name: 'marello_product_prod_supp_rel')]
#[ORM\UniqueConstraint(
    name: 'marello_product_prod_supp_rel_uidx',
    columns: ['product_id', 'supplier_id', 'quantity_of_unit']
)]
#[ORM\Entity(repositoryClass: ProductSupplierRelationRepository::class)]
class ProductSupplierRelation
{
    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Product
     */
    #[ORM\JoinColumn(name: 'product_id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Product::class, cascade: ['persist'], inversedBy: 'suppliers')]
    protected $product;

    /**
     * @var Supplier
     */
    #[ORM\JoinColumn(name: 'supplier_id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Supplier::class, cascade: ['persist'])]
    protected $supplier;

    /**
     * @var integer
     */
    #[ORM\Column(name: 'quantity_of_unit', type: Types::INTEGER, nullable: false)]
    protected $quantityOfUnit;

    /**
     * @var integer
     */
    #[ORM\Column(name: 'priority', type: Types::INTEGER)]
    protected $priority;

    /**
     * @var float
     */
    #[ORM\Column(name: 'cost', type: 'money', nullable: true)]
    protected $cost;

    /**
     * @var boolean
     */
    #[ORM\Column(name: 'can_dropship', type: Types::BOOLEAN, nullable: false)]
    protected $canDropship;

    /**
     * @var integer|null
     */
    #[ORM\Column(name: 'lead_time', type: Types::INTEGER, nullable: true)]
    protected $leadTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setSupplier(Supplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }

    public function getSupplier(): Supplier
    {
        return $this->supplier;
    }

    public function setQuantityOfUnit(int $quantityOfUnit): self
    {
        $this->quantityOfUnit = $quantityOfUnit;

        return $this;
    }

    public function getQuantityOfUnit(): int
    {
        return $this->quantityOfUnit;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setCost(?float $cost = null): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCanDropship(bool $canDropship): self
    {
        $this->canDropship = $canDropship;

        return $this;
    }

    public function getCanDropship(): bool
    {
        return $this->canDropship;
    }

    public function setLeadTime(int $leadTime): self
    {
        $this->leadTime = $leadTime;

        return $this;
    }

    public function getLeadTime(): ?int
    {
        return $this->leadTime;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
        }
    }
}
