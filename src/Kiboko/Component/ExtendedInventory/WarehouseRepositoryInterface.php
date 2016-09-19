<?php

namespace Kiboko\Component\ExtendedInventory;

use Marello\Component\Address\AddressInterface;
use Marello\Component\Inventory\WarehouseInterface;
use Marello\Component\Product\ProductInterface;
use Marello\Component\Inventory\WarehouseRepositoryInterface as MarelloWarehouseRepositoryInterface;

interface WarehouseRepositoryInterface extends MarelloWarehouseRepositoryInterface
{
    /**
     * Finds the most appropriate warehouse.
     *
     * @return WarehouseInterface
     */
    public function findMostAppropriate(AddressInterface $shippingAddress, ProductInterface $product);
}