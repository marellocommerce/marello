<?php

namespace Marello\Component\Inventory;

use Marello\Component\Product\ProductInterface;

interface InventoryItemRepositoryInterface
{
    /**
     * @param WarehouseInterface $warehouse
     * @param ProductInterface   $product
     *
     * @return null|InventoryItemInterface
     */
    public function findOneByWarehouseAndProduct(WarehouseInterface $warehouse, ProductInterface $product);

    /**
     * @param WarehouseInterface $warehouse
     * @param ProductInterface   $product
     *
     * @return InventoryItemInterface|null
     */
    public function findOrCreateByWarehouseAndProduct(WarehouseInterface $warehouse, ProductInterface $product);
}