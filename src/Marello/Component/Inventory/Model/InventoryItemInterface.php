<?php

namespace Marello\Component\Inventory\Model;


use Doctrine\Common\Collections\Collection;
use Marello\Component\Product\Model\ProductInterface;

interface InventoryItemInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return InventoryItemInterface
     */
    public function setId($id);

    /**
     * @return Product
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     *
     * @return $this
     */
    public function setProduct(ProductInterface $product = null);

    /**
     * @return WarehouseInterface
     */
    public function getWarehouse();

    /**
     * @param WarehouseInterface $warehouse
     *
     * @return $this
     */
    public function setWarehouse(WarehouseInterface $warehouse = null);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity);

    /**
     * @param int $amount
     *
     * @deprecated
     *
     * @return $this
     */
    public function modifyQuantity($amount);

    /**
     * @param int $amount
     *
     * @return $this
     */
    public function increaseQuantity($amount);

    /**
     * @param int $amount
     *
     * @return $this
     */
    public function decreaseQuantity($amount);

    /**
     * @return Collection
     */
    public function getInventoryLogs();

    /**
     * @param InventoryLogInterface $log
     *
     * @return $this
     */
    public function addInventoryLog(InventoryLogInterface $log);

    /**
     * @param InventoryLogInterface $log
     *
     * @return $this
     */
    public function removeInventoryLog(InventoryLogInterface $log);

    /**
     * @return mixed
     */
    public function getAllocatedQuantity();

    /**
     * @param mixed $allocatedQuantity
     *
     * @return $this
     */
    public function setAllocatedQuantity($allocatedQuantity);

    /**
     * @param mixed $amount
     *
     * @return $this
     */
    public function modifyAllocatedQuantity($amount);

    /**
     * @return InventoryAllocation[]|Collection
     */
    public function getAllocations();

    /**
     * @return int
     */
    public function getVirtualQuantity();
}
