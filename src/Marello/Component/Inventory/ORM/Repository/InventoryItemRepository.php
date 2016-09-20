<?php

namespace Marello\Component\Inventory\ORM\Repository;

use Doctrine\ORM\EntityRepository;
use Marello\Component\Inventory\Entity\InventoryItem;
use Marello\Component\Inventory\Model\InventoryItemInterface;
use Marello\Component\Inventory\Model\WarehouseInterface;
use Marello\Component\Inventory\Repository\InventoryItemRepositoryInterface;
use Marello\Component\Product\Model\ProductInterface;

class InventoryItemRepository extends EntityRepository implements InventoryItemRepositoryInterface
{
    /**
     * @param WarehouseInterface $warehouse
     * @param ProductInterface   $product
     *
     * @return null|InventoryItemInterface
     */
    public function findOneByWarehouseAndProduct(WarehouseInterface $warehouse, ProductInterface $product)
    {
        return $this->findOneBy(compact('warehouse', 'product'));
    }

    /**
     * @param WarehouseInterface $warehouse
     * @param ProductInterface   $product
     *
     * @return null|InventoryItemInterface
     */
    public function findOrCreateByWarehouseAndProduct(WarehouseInterface $warehouse, ProductInterface $product)
    {
        $inventoryItem = $this->findOneByWarehouseAndProduct($warehouse, $product);

        if (!$inventoryItem) {
            $inventoryItem = new InventoryItem();
            $inventoryItem
                ->setWarehouse($warehouse)
                ->setProduct($product);

            $this->getEntityManager()->persist($inventoryItem);
        }

        return $inventoryItem;
    }
}
