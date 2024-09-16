<?php

namespace Marello\Bundle\InventoryBundle\ImportExport\Strategy;

use Doctrine\Common\Util\ClassUtils;
use Marello\Bundle\InventoryBundle\Entity\InventoryBatch;
use Marello\Bundle\InventoryBundle\Model\InventoryUpdateContext;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Entity\InventoryLevel;
use Marello\Bundle\InventoryBundle\Event\InventoryUpdateEvent;
use Marello\Bundle\InventoryBundle\Model\InventoryUpdateContextFactory;
use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ImportExportBundle\Strategy\Import\ConfigurableAddOrReplaceStrategy;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;

class InventoryLevelUpdateStrategy extends ConfigurableAddOrReplaceStrategy
{
    const IMPORT_TRIGGER = 'import';
    const ALLOCATED_QTY = 0;

    private TokenAccessorInterface $tokenAccessor;

    public function setTokenAccessor(TokenAccessorInterface $tokenAccessor)
    {
        $this->tokenAccessor = $tokenAccessor;
    }

    /**
     * @param object|InventoryLevel $entity
     * @param bool                 $isFullData
     * @param bool                 $isPersistNew
     * @param array                $itemData
     * @param array                $searchContext
     * @param bool                 $entityIsRelation
     */
    protected function processEntity(
        $entity,
        $isFullData = false,
        $isPersistNew = false,
        $itemData = null,
        array $searchContext = [],
        $entityIsRelation = false
    ) {
        $organization = $this->tokenAccessor->getOrganization();
        if (!$organization) {
            return null;
        }
        // Set current organization to the entity to have possibility understand a scope of search for existing items
        $entity->setOrganization($organization);

        $inventoryUpdateQty = $entity->getInventoryQty();
        // find existing or new entity
        $existingEntity = $this->findInventoryLevel($entity);
        if ($existingEntity) {
            $this->checkEntityAcl($entity, $existingEntity, $itemData);
            $batchInventory = $existingEntity->getInventoryItem()->isEnableBatchInventory();
            $inventoryBatches = $entity->getInventoryBatches();
            if (!$batchInventory || $inventoryBatches->count() === 0) {
                $context = InventoryUpdateContextFactory::createInventoryLevelUpdateContext(
                    $existingEntity,
                    $existingEntity->getInventoryItem(),
                    [],
                    $inventoryUpdateQty,
                    self::ALLOCATED_QTY,
                    self::IMPORT_TRIGGER
                );
            } else {
                foreach ($inventoryBatches as $inventoryBatch) {
                    $inventoryBatch->setInventoryLevel($existingEntity);
                    $existingInventoryBatch = $this->findInventoryBatch($inventoryBatch);
                    if ($existingInventoryBatch) {
                        if ($inventoryBatch->getPurchasePrice() != null) {
                            $existingInventoryBatch->setPurchasePrice($inventoryBatch->getPurchasePrice());
                        }
                        if ($inventoryBatch->getExpirationDate() != null) {
                            $existingInventoryBatch->setExpirationDate($inventoryBatch->getExpirationDate());
                        }
                        $inventoryBatch = $existingInventoryBatch;
                    }
                    $batches = [
                        [
                            'batch' => $inventoryBatch,
                            'qty' => $inventoryUpdateQty,
                        ]
                    ];
                    $context = InventoryUpdateContextFactory::createInventoryLevelUpdateContext(
                        $existingEntity,
                        $existingEntity->getInventoryItem(),
                        $batches,
                        $inventoryUpdateQty,
                        self::ALLOCATED_QTY,
                        self::IMPORT_TRIGGER
                    );
                }
            }
        } else {
            $context = $this->createNewEntityContext($entity, $itemData);
        }

        if (!$this->context->getErrors()) {
            $this->eventDispatcher->dispatch(
                new InventoryUpdateEvent($context),
                InventoryUpdateEvent::NAME
            );
        }

        // deliberately return a different entity than the initial imported entity,
        // during errors with multiple runs of import
        $product = $this->getProduct($entity);
        if (!$product) {
            return null;
        }

        return $this->getInventoryItem($product);
    }

    protected function createNewEntityContext(InventoryLevel $entity, array $itemData): ?InventoryUpdateContext
    {
        $warehouse = $this->getWarehouse($entity);
        if (!$warehouse) {
            $error[] = $this->translator->trans('marello.inventory.messages.error.warehouse.not_found');
            $this->strategyHelper->addValidationErrors($error, $this->context);

            return null;
        }

        $this->checkEntityAcl($entity, null, $itemData);
        $product = $this->getProduct($entity);
        if (!$product) {
            $this->addProductNotExistValidationError($itemData);

            return null;
        }

        $inventoryItem = $this->getInventoryItem($product);
        if (!$inventoryItem) {
            $this->addInventoryItemNotExistValidationError($itemData);

            return null;
        }

        $inventoryUpdateQty = $entity->getInventoryQty();
        $newEntityKey = $this->createSerializedEntityKey(
            $entity,
            $inventoryItem,
            $warehouse->getCode()
        );

        if ($this->newEntitiesHelper->getEntity($newEntityKey)) {
            $this->addDuplicateValidationError($itemData);

            return null;
        }

        $this->newEntitiesHelper->setEntity($newEntityKey, $entity);

        return InventoryUpdateContextFactory::createInventoryUpdateContext(
            $product,
            $inventoryItem,
            $inventoryUpdateQty,
            self::ALLOCATED_QTY,
            self::IMPORT_TRIGGER
        );
    }

    private function createSerializedEntityKey(
        InventoryLevel $entity,
        InventoryItem $inventoryItem,
        string $warehouseCode
    ): string {
        $entityClass = ClassUtils::getClass($entity);

        return sprintf('%s:%s', $entityClass, serialize([$inventoryItem->getId(), $warehouseCode]));
    }

    protected function addDuplicateValidationError(array $itemData): void
    {
        $error = $this->translator->trans(
            'marello.inventory.messages.error.inventorylevel.duplicate_entry',
            ['%entity_sku%' => $itemData['inventoryItem']['product']['sku']]
        );

        $this->context->addError($error);
    }

    protected function addProductNotExistValidationError(array $itemData): void
    {
        $error[] = $this->translator->trans(
            'marello.inventory.messages.error.inventorylevel.product_not_found',
            ['%entity_sku%' => $itemData['inventoryItem']['product']['sku']]
        );
        $this->strategyHelper->addValidationErrors($error, $this->context);
    }

    protected function addInventoryItemNotExistValidationError(array $itemData): void
    {
        $error[] = $this->translator->trans(
            'marello.inventory.messages.error.inventorylevel.item_not_found',
            ['%entity_sku%' => $itemData['inventoryItem']['product']['sku']]
        );
        $this->strategyHelper->addValidationErrors($error, $this->context);
    }

    protected function findInventoryLevel($entity): ?InventoryLevel
    {
        $product = $this->getProduct($entity);
        if (!$product) {
            return null;
        }

        $inventoryItem = $this->getInventoryItem($product);
        if (!$inventoryItem) {
            return null;
        }

        $warehouse = $this->getWarehouse($entity);
        if (!$warehouse) {
            return null;
        }

        /** @var InventoryLevel $level */
        $level = $this->databaseHelper->findOneBy(InventoryLevel::class, [
            'inventoryItem' => $inventoryItem->getId(),
            'warehouse'     => $warehouse->getId()
        ]);

        return $level;
    }

    protected function findInventoryBatch(InventoryBatch $entity): ?InventoryBatch
    {
        if (!$entity->getBatchNumber()) {
            return null;
        }
        $invLevel = $entity->getInventoryLevel();
        if (!$invLevel->getId()) {
            return null;
        }

        return $this->databaseHelper->findOneBy(InventoryBatch::class, [
            'inventoryLevel' => $invLevel->getId(),
            'batchNumber'    => $entity->getBatchNumber()
        ]);
    }

    protected function getProduct(InventoryLevel $entity): ?Product
    {
        return $this->databaseHelper
            ->findOneBy(
                Product::class,
                [
                    'sku' => $entity->getInventoryItem()->getProduct()->getSku(),
                    'organization' => $entity->getOrganization()
                ]
            );
    }

    protected function getInventoryItem(Product $entity): ?InventoryItem
    {
        return $this->databaseHelper->findOneBy(InventoryItem::class, ['product' => $entity->getId()]);
    }

    protected function getWarehouse(InventoryLevel $entity): ?Warehouse
    {
        return $this->databaseHelper->findOneBy(Warehouse::class, ['code' => $entity->getWarehouse()->getCode()]);
    }

    protected function updateContextCounters($entity)
    {
        $identifier = $this->databaseHelper->getIdentifier($entity);
        if ($identifier) {
            $this->context->incrementUpdateCount();
        } else {
            $this->context->incrementAddCount();
        }
    }
}
