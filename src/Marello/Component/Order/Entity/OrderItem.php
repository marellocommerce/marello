<?php

namespace Marello\Component\Order\Entity;

use Brick\Math\BigDecimal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as JMS;

use Marello\Component\Inventory\Entity\InventoryAllocation;
use Marello\Component\Order\Model\OrderInterface;
use Marello\Component\Order\Model\OrderItemInterface;
use Marello\Component\Product\ProductInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

use Marello\Component\Order\Model\ExtendOrderItem;
use Marello\Bundle\ReturnBundle\Entity\ReturnItem;
use Marello\Component\Pricing\Model\CurrencyAwareInterface;
use Marello\Component\Inventory\InventoryAllocation\AllocationTargetInterface;

/**
 * @ORM\Entity()
 * @Oro\Config()
 * @ORM\Table(name="marello_order_order_item")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("ALL")
 */
class OrderItem extends ExtendOrderItem implements AllocationTargetInterface, CurrencyAwareInterface, OrderItemInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Marello\Bundle\ProductBundle\Entity\Product")
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @JMS\Expose
     */
    protected $product;

    /**
     * @var string
     *
     * @ORM\Column(name="product_name",type="string", nullable=false)
     */
    protected $productName;

    /**
     * @var string
     *
     * @ORM\Column(name="product_sku",type="string", nullable=false)
     */
    protected $productSku;

    /**
     * @var OrderInterface
     *
     * @ORM\ManyToOne(targetEntity="Marello\Component\Order\Entity\Order", inversedBy="items")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $order;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity",type="integer",nullable=false)
     *
     * @JMS\Expose
     */
    protected $quantity;

    /**
     * @var BigDecimal
     *
     * @ORM\Column(name="price",type="money")
     *
     * @JMS\Expose
     */
    protected $price;

    /**
     * @var BigDecimal
     *
     * @ORM\Column(name="tax",type="money")
     *
     * @JMS\Expose
     */
    protected $tax;

    /**
     * @var float
     *
     * @ORM\Column(name="tax_percent", type="percent", nullable=true)
     * @JMS\Expose
     */
    protected $taxPercent;

    /**
     * @var float
     *
     * @ORM\Column(name="discount_percent", type="percent", nullable=true)
     * @JMS\Expose
     */
    protected $discountPercent;

    /**
     * @var BigDecimal
     *
     * @ORM\Column(name="discount_amount", type="money", nullable=true)
     * @JMS\Expose
     */
    protected $discountAmount;

    /**
     * @var BigDecimal
     *
     * @ORM\Column(name="total_price",type="money", nullable=false)
     *
     * @JMS\Expose
     */
    protected $totalPrice;

    /**
     * @var ReturnItem[]|Collection
     *
     * @ORM\OneToMany(targetEntity="Marello\Bundle\ReturnBundle\Entity\ReturnItem", mappedBy="orderItem", cascade={})
     */
    protected $returnItems;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Marello\Component\Inventory\Entity\InventoryAllocation",
     *     mappedBy="targetOrderItem",
     *     cascade={}
     * )
     *
     * @var InventoryAllocation[]|Collection
     */
    protected $inventoryAllocations;

    /**x
     * OrderItem constructor.
     */
    public function __construct()
    {
        $this->returnItems = new ArrayCollection();
        $this->inventoryAllocations = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->productName = $this->product->getName();
        $this->productSku  = $this->product->getSku();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param OrderInterface $order
     *
     * @return $this
     */
    public function setOrder(OrderInterface $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return BigDecimal
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param BigDecimal $price
     *
     * @return $this
     */
    public function setPrice(BigDecimal $price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return BigDecimal
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param BigDecimal $tax
     *
     * @return $this
     */
    public function setTax(BigDecimal $tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * @return BigDecimal
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param BigDecimal $totalPrice
     *
     * @return $this
     */
    public function setTotalPrice(BigDecimal $totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
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
     *
     * @return $this
     */
    public function setProduct(ProductInterface $product)
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
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @return Collection|ReturnItem[]
     */
    public function getReturnItems()
    {
        return $this->returnItems;
    }

    /**
     * @return float
     */
    public function getTaxPercent()
    {
        return $this->taxPercent;
    }

    /**
     * @param float $taxPercent
     */
    public function setTaxPercent($taxPercent)
    {
        $this->taxPercent = $taxPercent;
    }

    /**
     * @return float
     */
    public function getDiscountPercent()
    {
        return $this->discountPercent;
    }

    /**
     * @param float $discountPercent
     */
    public function setDiscountPercent($discountPercent)
    {
        $this->discountPercent = $discountPercent;
    }

    /**
     * @return BigDecimal
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * @param BigDecimal $discountAmount
     */
    public function setDiscountAmount(BigDecimal $discountAmount)
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     * Returns name of property, that this entity is mapped to InventoryAllocation under.
     *
     * @return string
     */
    public static function getAllocationPropertyName()
    {
        return 'OrderItem';
    }

    /**
     * @return Collection|InventoryAllocation[]
     */
    public function getInventoryAllocations()
    {
        return $this->inventoryAllocations;
    }

    /**
     * Get currency for orderItem from Order
     */
    public function getCurrency()
    {
        return $this->order->getCurrency();
    }
}
