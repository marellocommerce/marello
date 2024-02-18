<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Strategy;

use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeFamily;
use Oro\Bundle\LocaleBundle\ImportExport\Strategy\LocalizedFallbackValueAwareStrategy;

class ProductStrategy extends LocalizedFallbackValueAwareStrategy
{
    public const DELIMITER = ', ';

    protected function processEntity(
        $entity,
        $isFullData = false,
        $isPersistNew = false,
        $itemData = null,
        array $searchContext = [],
        $entityIsRelation = false
    ) {
        /** @var Product $entity */
        $entity->setType(Product::DEFAULT_PRODUCT_TYPE);
        $this->entityOwnershipAssociationsSetter->setOwnershipAssociations($entity);
        $result = $this->processStatus($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processAttributeFamily($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processTaxCode($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processCategories($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processChannels($entity);
        if (!$result) {
            return null;
        }

        return $result;
    }

    private function processStatus(Product $entity): ?Product
    {
        $status = $entity->getStatus();
        if ($status) {
            $existingStatus = $this->findExistingEntity($status);
            if ($existingStatus) {
                $entity->setStatus($existingStatus);
            } else {
                $this->processValidationErrors(
                    $entity,
                    [
                        $this->translator->trans('marello.product.messages.import.error.status.invalid')
                    ]
                );

                return null;
            }
        }

        return $entity;
    }

    private function processAttributeFamily(Product $entity): ?Product
    {
        $attributeFamily = $entity->getAttributeFamily();
        if ($attributeFamily) {
            $existingAttributeFamily = $this->findEntityByIdentityValues(
                AttributeFamily::class,
                ['code' => $attributeFamily->getCode()]
            );
            if ($existingAttributeFamily instanceof AttributeFamily) {
                $entity->setAttributeFamily($existingAttributeFamily);
            } else {
                $this->processValidationErrors(
                    $entity,
                    [
                        $this->translator->trans('marello.product.messages.import.error.attribute_family.invalid')
                    ]
                );

                return null;
            }
        } else {
            $this->processValidationErrors(
                $entity,
                [
                    $this->translator->trans('marello.product.messages.import.error.attribute_family.required')
                ]
            );

            return null;
        }

        return $entity;
    }

    private function processTaxCode(Product $entity): ?Product
    {
        $taxCode = $entity->getTaxCode();
        if ($taxCode) {
            $existingTaxCode = $this->findEntityByIdentityValues(
                TaxCode::class,
                ['code' => $taxCode->getCode()]
            );
            if ($existingTaxCode instanceof TaxCode) {
                $entity->setTaxCode($existingTaxCode);
            } else {
                $this->processValidationErrors(
                    $entity,
                    [
                        $this->translator->trans('marello.product.messages.import.error.tax_code.invalid')
                    ]
                );

                return null;
            }
        } else {
            $this->processValidationErrors(
                $entity,
                [
                    $this->translator->trans('marello.product.messages.import.error.tax_code.required')
                ]
            );

            return null;
        }

        return $entity;
    }

    private function processCategories(Product $entity): ?Product
    {
        /** @var Category $category */
        $category = $entity->getCategories()->first();
        if ($category) {
            $entity->removeCategory($category);
            $codes = explode(self::DELIMITER, $category->getCode());
            foreach ($codes as $code) {
                $existingCategory = $this->findEntityByIdentityValues(
                    Category::class,
                    ['code' => $code]
                );

                if ($existingCategory instanceof Category) {
                    $entity->addCategory($existingCategory);
                } else {
                    $this->clearFilledCollections($entity);
                    $this->processValidationErrors(
                        $entity,
                        [
                            $this->translator->trans(
                                'marello.product.messages.import.error.categories.invalid',
                                ['code' => $code]
                            )
                        ]
                    );

                    return null;
                }
            }
        }

        return $entity;
    }

    private function processChannels(Product $entity): ?Product
    {
        /** @var SalesChannel $channel */
        $channel = $entity->getChannels()->first();
        if ($channel) {
            $entity->removeChannel($channel);
            $codes = explode(self::DELIMITER, $channel->getCode());
            foreach ($codes as $code) {
                $existingChannel = $this->findEntityByIdentityValues(
                    SalesChannel::class,
                    ['code' => $code]
                );

                if ($existingChannel instanceof SalesChannel) {
                    $entity->addChannel($existingChannel);
                } else {
                    $this->clearFilledCollections($entity);
                    $this->processValidationErrors(
                        $entity,
                        [
                            $this->translator->trans(
                                'marello.product.messages.import.error.channels.invalid',
                                ['code' => $code]
                            )
                        ]
                    );

                    return null;
                }
            }
        }

        return $entity;
    }

    private function clearFilledCollections(Product $product): void
    {
        // Clear categories/channels collections to avoid cascade persist error if we get an error
        foreach ($product->getCategories() as $category) {
            $category->removeProduct($product);
        }
        foreach ($product->getChannels() as $channel) {
            $channel->removeProduct($product);
        }
    }
}
