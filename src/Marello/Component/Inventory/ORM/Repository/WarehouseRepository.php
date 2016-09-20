<?php

namespace Marello\Component\Inventory\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Marello\Component\Inventory\Model\WarehouseInterface;
use Marello\Component\Inventory\Repository\WarehouseRepositoryInterface;

class WarehouseRepository extends EntityRepository implements WarehouseRepositoryInterface
{
    /**
     * Finds default warehouse.
     *
     * @return WarehouseInterface
     */
    public function getDefault()
    {
        return $this->findOneBy(['default' => true]);
    }
}
