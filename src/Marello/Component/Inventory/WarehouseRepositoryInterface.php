<?php

namespace Marello\Component\Inventory;


interface WarehouseRepositoryInterface
{
    /**
     * Finds default warehouse.
     *
     * @return WarehouseInterface
     */
    public function getDefault();
}