<?php

namespace Marello\Bundle\InventoryBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Component\Inventory\InventoryItemRepositoryInterface;
use Marello\Component\Inventory\WarehouseInterface;

class InventoryItemRepository extends EntityRepository implements InventoryItemRepositoryInterface
{
    /**
     * @param WarehouseInterface $warehouse
     * @param Product            $product
     *
     * @return null|InventoryItem
     */
    public function findOneByWarehouseAndProduct(WarehouseInterface $warehouse, Product $product)
    {
        return $this->findOneBy(compact('warehouse', 'product'));
    }

    /**
     * @param WarehouseInterface $warehouse
     * @param Product            $product
     *
     * @return InventoryItem|null
     */
    public function findOrCreateByWarehouseAndProduct(WarehouseInterface $warehouse, Product $product)
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
