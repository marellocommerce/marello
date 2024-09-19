<?php

namespace Marello\Bundle\PackingBundle\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Marello\Bundle\InventoryBundle\Entity\Allocation;
use Marello\Bundle\InventoryBundle\Entity\AllocationItem;
use Marello\Bundle\InventoryBundle\Entity\InventoryBatch;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\PackingBundle\Entity\PackingSlip;
use Marello\Bundle\PackingBundle\Entity\PackingSlipItem;
use Marello\Bundle\ProductBundle\Entity\Product;

class OrderToPackingSlipMapper extends AbstractPackingSlipMapper
{
    /**
     * {@inheritdoc}
     */
    public function map($sourceEntity)
    {
        if (!($sourceEntity instanceof Allocation)) {
            throw new \InvalidArgumentException(
                sprintf('Wrong source entity "%s" provided to OrderToPackingSlipMapper', get_class($sourceEntity))
            );
        }
        /** @var Allocation $sourceEntity */
        $packingSlip = new PackingSlip();
        $data = $this->getData($sourceEntity->getOrder(), PackingSlip::class);
        $data['order'] = $sourceEntity->getOrder();
        $data['warehouse'] = $sourceEntity->getWarehouse();
        $data['sourceEntity'] = $sourceEntity;
        $data['items'] = $this->getItems($sourceEntity);

        $this->assignData($packingSlip, $data);

        return [$packingSlip];
    }

    /**
     * @param Collection $items
     * @return ArrayCollection
     */
    protected function getItems(Allocation $sourceEntity)
    {
        $allocationItems = $sourceEntity->getItems()->toArray();
        $packingSlipItems = [];
        /** @var AllocationItem $item */
        foreach ($allocationItems as $item) {
            $packingSlipItems[] = $this->mapItem($item, $sourceEntity->getWarehouse());
        }

        return new ArrayCollection($packingSlipItems);
    }

    /**
     * @param AllocationItem $allocationItem
     * @return PackingSlipItem
     */
    protected function mapItem($allocationItem, Warehouse $warehouse)
    {
        $packingSlipItem = new PackingSlipItem();
        $packingSlipItemData = $this->getData($allocationItem, PackingSlipItem::class);
        $product = $allocationItem->getProduct();

        $packingSlipItemData['inventoryBatches'] = $allocationItem->getInventoryBatches();
        $packingSlipItemData['weight'] = ($product->getWeight() * $allocationItem->getQuantity());
        $packingSlipItemData['orderItem'] = $allocationItem->getOrderItem();
        $packingSlipItemData['productUnit'] = $allocationItem->getOrderItem()->getProductUnit();
        $this->assignData($packingSlipItem, $packingSlipItemData);

        return $packingSlipItem;
    }
}
