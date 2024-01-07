<?php

namespace Marello\Bundle\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\SupplierBundle\Entity\Supplier;

/**
 * ProductSupplierRelation
 *
 * @ORM\Table(
 *     name="marello_product_prod_supp_rel",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(
 *              name="marello_product_prod_supp_rel_uidx",
 *              columns={"product_id", "supplier_id", "quantity_of_unit"}
 *          )
 *      }
 * )
 * @ORM\Entity(repositoryClass="Marello\Bundle\ProductBundle\Entity\Repository\ProductSupplierRelationRepository")
 */
class ProductSupplierRelation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Marello\Bundle\ProductBundle\Entity\Product", inversedBy="suppliers",
     *      cascade={"persist"})
     * @ORM\JoinColumn(name="product_id", nullable=false, onDelete="CASCADE")
     */
    protected $product;

    /**
     * @var Supplier
     *
     * @ORM\ManyToOne(targetEntity="Marello\Bundle\SupplierBundle\Entity\Supplier", cascade={"persist"})
     * @ORM\JoinColumn(name="supplier_id", nullable=false, onDelete="CASCADE")
     */
    protected $supplier;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity_of_unit", type="integer", nullable=false)
     */
    protected $quantityOfUnit;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer")
     */
    protected $priority;

    /**
     * @var float
     *
     * @ORM\Column(name="cost", type="money", nullable=true)
     */
    protected $cost;

    /**
     * @var boolean
     *
     * @ORM\Column(name="can_dropship", type="boolean", nullable=false)
     */
    protected $canDropship;

    /**
     * @var integer
     *
     * @ORM\Column(name="lead_time", type="integer", nullable=true)
     */
    protected $leadTime = 0;

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
