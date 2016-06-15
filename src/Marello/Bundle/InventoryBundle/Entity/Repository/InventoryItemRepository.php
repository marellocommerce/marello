<?php

namespace Marello\Bundle\InventoryBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Component\Inventory\InventoryItemRepositoryInterface;
use Marello\Component\Inventory\WarehouseInterface;
use Marello\Component\Product\ProductInterface;

class InventoryItemRepository extends EntityRepository implements InventoryItemRepositoryInterface
{
    /**
     * @param WarehouseInterface $warehouse
     * @param ProductInterface   $product
     *
     * @return null|InventoryItem
     */
    public function findOneByWarehouseAndProduct(WarehouseInterface $warehouse, ProductInterface $product)
    {
        return $this->findOneBy(compact('warehouse', 'product'));
    }

    /**
     * @param WarehouseInterface $warehouse
     * @param ProductInterface   $product
     *
     * @return InventoryItem|null
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
