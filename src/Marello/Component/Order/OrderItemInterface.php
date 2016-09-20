<?php

namespace Marello\Component\Order;

use Brick\Math\BigDecimal;
use Doctrine\Common\Collections\Collection;

use Marello\Bundle\ReturnBundle\Entity\ReturnItem;
use Marello\Component\Pricing\Model\CurrencyAwareInterface;
use Marello\Component\Inventory\InventoryAllocation\AllocationTargetInterface;
use Marello\Component\Product\ProductInterface;

interface OrderItemInterface extends AllocationTargetInterface, CurrencyAwareInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * @param OrderInterface $order
     *
     * @return $this
     */
    public function setOrder(OrderInterface $order = null);

    /**
     * @return float
     */
    public function getQuantity();

    /**
     * @param float $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity);

    /**
     * @return BigDecimal
     */
    public function getPrice();

    /**
     * @param BigDecimal $price
     *
     * @return $this
     */
    public function setPrice(BigDecimal $price);

    /**
     * @return BigDecimal
     */
    public function getTax();

    /**
     * @param BigDecimal $tax
     *
     * @return $this
     */
    public function setTax(BigDecimal $tax);

    /**
     * @return BigDecimal
     */
    public function getTotalPrice();

    /**
     * @param BigDecimal $totalPrice
     *
     * @return $this
     */
    public function setTotalPrice(BigDecimal $totalPrice);

    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     *
     * @return $this
     */
    public function setProduct(ProductInterface $product);

    /**
     * @return string
     */
    public function getProductSku();

    /**
     * @return string
     */
    public function getProductName();

    /**
     * @return Collection|ReturnItem[]
     */
    public function getReturnItems();

    /**
     * @return float
     */
    public function getTaxPercent();

    /**
     * @param float $taxPercent
     */
    public function setTaxPercent($taxPercent);

    /**
     * @return float
     */
    public function getDiscountPercent();

    /**
     * @param float $discountPercent
     */
    public function setDiscountPercent($discountPercent);

    /**
     * @return float
     */
    public function getDiscountAmount();

    /**
     * @param BigDecimal $discountAmount
     */
    public function setDiscountAmount(BigDecimal $discountAmount);
}
