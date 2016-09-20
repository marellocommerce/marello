<?php

namespace Marello\Component\Inventory\Repository;

use Marello\Component\Inventory\Model\WarehouseInterface;

interface WarehouseRepositoryInterface
{
    /**
     * Finds default warehouse.
     *
     * @return WarehouseInterface
     */
    public function getDefault();
}
