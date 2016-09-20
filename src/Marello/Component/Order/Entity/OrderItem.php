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
use Marello\Component\Product\Model\ProductInterface;
use Marello\Component\RMA\Model\ReturnItemInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

use Marello\Component\Order\Model\ExtendOrderItem;
use Marello\Component\RMA\Entity\ReturnItem;
use Marello\Component\Pricing\Model\CurrencyAwareInterface;
use Marello\Component\Inventory\InventoryAllocation\AllocationTargetInterface;

class OrderItem extends ExtendOrderItem implements AllocationTargetInterface, CurrencyAwareInterface, OrderItemInterface
{
    /**
     * @var int
     * 
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var ProductInterface
     * 
     * @JMS\Expose
     */
    protected $product;

    /**
     * @var string
     */
    protected $productName;

    /**
     * @var string
     */
    protected $productSku;

    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var int
     *
     * @JMS\Expose
     */
    protected $quantity;

    /**
     * @var BigDecimal
     *
     * @JMS\Expose
     */
    protected $price;

    /**
     * @var BigDecimal
     *
     * @JMS\Expose
     */
    protected $tax;

    /**
     * @var float
     * 
     * @JMS\Expose
     */
    protected $taxPercent;

    /**
     * @var float
     *
     * @JMS\Expose
     */
    protected $discountPercent;

    /**
     * @var BigDecimal
     *
     * @JMS\Expose
     */
    protected $discountAmount;

    /**
     * @var BigDecimal
     *
     * @JMS\Expose
     */
    protected $totalPrice;

    /**
     * @var ReturnItemInterface[]|Collection
     */
    protected $returnItems;

    /**
     * @var InventoryAllocationInterface[]|Collection
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
