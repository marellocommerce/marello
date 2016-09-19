<?php

namespace Marello\Bundle\InventoryBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Marello\Component\Inventory\WarehouseInterface;
use Marello\Component\Inventory\WarehouseRepositoryInterface;

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
