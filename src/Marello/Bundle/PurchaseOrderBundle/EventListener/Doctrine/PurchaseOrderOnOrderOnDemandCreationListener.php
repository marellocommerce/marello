<?php

namespace Marello\Bundle\PurchaseOrderBundle\EventListener\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Marello\Bundle\InventoryBundle\Entity\Allocation;
use Marello\Bundle\InventoryBundle\Entity\AllocationItem;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Entity\InventoryLevel;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\InventoryBundle\Entity\WarehouseChannelGroupLink;
use Marello\Bundle\InventoryBundle\Factory\InventoryBatchFromInventoryLevelFactory;
use Marello\Bundle\InventoryBundle\Provider\AllocationContextInterface;
use Marello\Bundle\InventoryBundle\Provider\AllocationStateStatusInterface;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\PricingBundle\Entity\ProductPrice;
use Marello\Bundle\PurchaseOrderBundle\Entity\PurchaseOrder;
use Marello\Bundle\PurchaseOrderBundle\Entity\PurchaseOrderItem;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;

class PurchaseOrderOnOrderOnDemandCreationListener
{
    const ORDER_ON_DEMAND = 'order_on_demand';
    
    /**
     * @var int
     */
    private $allocationId;

    /**
     * @var ConfigManager $configManager
     */
    private $configManager;

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Allocation) {
            return;
        }

        if (!$this->configManager->get('marello_inventory.inventory_on_demand_enabled')
            || !$this->configManager->get('marello_inventory.inventory_on_demand')
        ) {
            return;
        }

        if ($entity->getAllocationContext()
            && $entity->getAllocationContext()->getId() === AllocationContextInterface::ALLOCATION_CONTEXT_REALLOCATION
        ) {
            return;
        }
        $orderOnDemandItems = [];
        foreach ($entity->getItems() as $item) {
            /** @var InventoryItem $inventoryItem */
            $inventoryItem = $item->getOrderItem()->getInventoryItems()->first();
            if ($inventoryItem->isEnableBatchInventory()
                && $this->isOrderOnDemandItem($item->getProduct())
                && $entity->getState()->getId() === AllocationStateStatusInterface::ALLOCATION_STATE_WFS
            ) {
                $orderOnDemandItems[] = $item;
            }
        }
        if (!empty($orderOnDemandItems)) {
            $this->allocationId = $entity->getId();
        }
    }
    
    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (!$this->allocationId) {
            return;
        }

        $entityManager = $args->getEntityManager();
        /** @var Allocation $allocation */
        $allocation = $entityManager
            ->getRepository(Allocation::class)
            ->find($this->allocationId);
        $this->allocationId = null;
        if (!$allocation) {
            return;
        }

        [$poBySupplier, $allocationItemsBySupplier] = $this->createPurchaseOrdersFromAllocation(
            $allocation,
            $entityManager
        );
        $this->updatePurchaseOrdersTotal($poBySupplier, $allocationItemsBySupplier, $allocation->getOrder());
        $entityManager->flush();
    }

    private function createPurchaseOrdersFromAllocation(
        Allocation $allocation,
        EntityManager $entityManager
    ): array {
        $poBySupplier = [];
        $allocationItemsBySupplier = [];

        $warehouse = $this->getOnDemandLocation($allocation, $entityManager);
        if (!$warehouse) {
            throw new \LogicException('To create Purchase Order you need to specify an On Demand Location warehouse');
        }

        $organization = $allocation->getOrganization();
        foreach ($allocation->getItems() as $allocationItem) {
            if (!$this->isOrderOnDemandItem($allocationItem->getProduct())) {
                continue;
            }

            $product = $allocationItem->getProduct();
            $supplier = $product->getPreferredSupplier();
            $supplierCode = $supplier->getCode();
            $allocationItemsBySupplier[$supplierCode][] = $allocationItem->getId();

            // Create Purchase Order if not exist
            if (!isset($poBySupplier[$supplierCode])) {
                $po = new PurchaseOrder();
                $po
                    ->setSupplier($supplier)
                    ->setOrganization($organization)
                    ->setWarehouse($warehouse);

                $entityManager->persist($po);
                $poBySupplier[$supplierCode] = $po;
            }

            /** @var PurchaseOrder $po */
            $po = $poBySupplier[$supplierCode];
            $price = $this->getPurchasePrice($product, $allocation->getOrder());
            $poItem = new PurchaseOrderItem();
            $poItem
                ->setProduct($product)
                ->setOrderedAmount($allocationItem->getQuantity())
                ->setRowTotal($price->getValue() * $allocationItem->getQuantity())
                ->setPurchasePrice($price)
                ->setData([
                    self::ORDER_ON_DEMAND =>
                        [
                            'order' => $allocation->getOrder()->getId(),
                            'allocationItem' => $allocationItem->getId(),
                        ]
                ]);

            $po->addItem($poItem);
            $entityManager->persist($poItem);

            $this->createInventoryBatch($allocationItem, $warehouse, $entityManager);
        }

        return [$poBySupplier, $allocationItemsBySupplier];
    }

    private function updatePurchaseOrdersTotal(array $poBySupplier, array $allocationItemsBySupplier, Order $order): void
    {
        foreach ($poBySupplier as $po) {
            $orderTotal = 0.00;
            foreach ($po->getItems() as $poi) {
                $orderTotal += $poi->getRowTotal();
            }

            $po
                ->setOrderTotal($orderTotal)
                ->setData(
                    [
                        self::ORDER_ON_DEMAND =>
                            [
                                'order' => $order->getId(),
                                'allocationItems' => $allocationItemsBySupplier[$po->getSupplier()->getCode()]
                            ]
                    ]
                );
        }
    }

    /**
     * @param Product $product
     * @param Order $order
     * @return ProductPrice|null
     */
    private function getPurchasePrice(Product $product, Order $order)
    {
        $supplier = $product->getPreferredSupplier();
        $productPrice = new ProductPrice();
        $productPrice
            ->setValue(0)
            ->setProduct($product)
            ->setCurrency($order->getCurrency());
        foreach ($product->getSuppliers() as $productSupplierRelation) {
            if ($productSupplierRelation->getSupplier() === $supplier) {
                $productPrice
                    ->setValue($productSupplierRelation->getCost())
                    ->setProduct($product)
                    ->setCurrency($supplier->getCurrency());
                return $productPrice;
            }
        }

        return $productPrice;
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function isOrderOnDemandItem(Product $product)
    {
        foreach ($product->getInventoryItems() as $inventoryItem) {
            if ($inventoryItem->isOrderOnDemandAllowed()) {
                return true;
            }
        }

        return false;
    }

    private function createInventoryBatch(
        AllocationItem $allocationItem,
        Warehouse $warehouse,
        EntityManager $entityManager
    ): void {
        $allocation = $allocationItem->getAllocation();

        /** @var InventoryLevel[] $inventoryLevels */
        $inventoryLevels = $allocationItem->getProduct()->getInventoryItems()->first()->getInventoryLevels();
        foreach ($inventoryLevels as $inventoryLevel) {
            if ($inventoryLevel->getWarehouse() !== $warehouse) {
                continue;
            }

            $inventoryBatch = InventoryBatchFromInventoryLevelFactory::createInventoryBatch($inventoryLevel);
            $inventoryBatch->setOrganization($allocation->getOrganization());
            $inventoryBatch->setOrderOnDemandRef($allocationItem->getId());
            $inventoryBatch->setQuantity($allocationItem->getQuantity());
            $inventoryBatch->setDeliveryDate(null);

            $entityManager->persist($inventoryBatch);
        }
    }

    private function getOnDemandLocation(Allocation $allocation, EntityManager $entityManager): ?Warehouse
    {
        /** @var WarehouseChannelGroupLink $channelGroupLink */
        $channelGroupLink = $entityManager
            ->getRepository(WarehouseChannelGroupLink::class)
            ->findLinkBySalesChannelGroup($allocation->getOrder()->getSalesChannel()->getGroup());
        $onDemandLocations = $entityManager->getRepository(Warehouse::class)->findBy(
            [
                'orderOnDemandLocation' => true,
                'group' => $channelGroupLink->getWarehouseGroup(),
            ],
            ['sortOrder' => 'ASC'],
            1
        );

        return $onDemandLocations[0] ?? null;
    }

    /**
     * @param ConfigManager $configManager
     */
    public function setConfigManager(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }
}
