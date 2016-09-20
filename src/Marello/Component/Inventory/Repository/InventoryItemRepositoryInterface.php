<?php

namespace Marello\Component\Inventory\Repository;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;
use Marello\Component\Inventory\Model\InventoryItemInterface;
use Marello\Component\Inventory\Model\WarehouseInterface;
use Marello\Component\Product\ProductInterface;

interface InventoryItemRepositoryInterface extends ObjectRepository, Selectable
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
