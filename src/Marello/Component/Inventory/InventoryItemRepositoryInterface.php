<?php

namespace Marello\Component\Inventory;

use Marello\Bundle\ProductBundle\Entity\Product;

interface InventoryItemRepositoryInterface
{
    /**
     * @param WarehouseInterface $warehouse
     * @param Product            $product
     *
     * @return null|InventoryItemInterface
     */
    public function findOneByWarehouseAndProduct(WarehouseInterface $warehouse, Product $product);

    /**
     * @param WarehouseInterface $warehouse
     * @param Product            $product
     *
     * @return InventoryItemInterface|null
     */
    public function findOrCreateByWarehouseAndProduct(WarehouseInterface $warehouse, Product $product);
}