<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Strategy;

use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\AttachmentBundle\Tools\MimeTypeChecker;
use Oro\Bundle\LocaleBundle\ImportExport\Strategy\LocalizedFallbackValueAwareStrategy;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class ProductStrategy extends LocalizedFallbackValueAwareStrategy
{
    public const DELIMITER = ', ';

    /**
     * @var MimeTypeChecker
     */
    private $mimeTypeChecker;

    public function setMimeTypeChecker(MimeTypeChecker $mimeTypeChecker)
    {
        $this->mimeTypeChecker = $mimeTypeChecker;
    }

    protected function importEntityFields(
        $entity,
        $existingEntity,
        $isFullData,
        $entityIsRelation,
        $itemData
    ) {
        // Because the import process can't replace the image properly, we store this attachment before parent call
        // And then reuse during the image processing below
        $newImage = null;
        if ($entity instanceof Product && $entity->getImage()) {
            $newImage = $entity->getImage();
        }

        $entity = parent::importEntityFields($entity, $existingEntity, $isFullData, $entityIsRelation, $itemData);
        if (!$entity instanceof Product) {
            return $entity;
        }

        /** @var Product $entity */
        $entity->setType(Product::DEFAULT_PRODUCT_TYPE);
        $result = $this->processAttributeFamily($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processTaxCode($entity);
        if (!$result) {
            return null;
        }

        $result = $this->processCategories($entity, $itemData);
        if (!$result) {
            return null;
        }

        $result = $this->processChannels($entity, $itemData);
        if (!$result) {
            return null;
        }

        $result = $this->processImage($entity, $itemData, $newImage);
        if (!$result) {
            return null;
        }

        return $result;
    }

    protected function getObjectValue($entity, $fieldName)
    {
        if ($entity instanceof Product && \in_array($fieldName, ['channels', 'categories'], true)) {
            return null;
        }

        return parent::getObjectValue($entity, $fieldName);
    }

    private function processAttributeFamily(Product $entity): ?Product
    {
        $attributeFamily = $entity->getAttributeFamily();
        if (!$attributeFamily) {
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
        if (!$taxCode) {
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

    private function processCategories(Product $entity, array $itemData): ?Product
    {
        if (!array_key_exists('categories', $itemData)) {
            return $entity;
        }

        $entity->clearCategories();
        if (empty($itemData['categories'][0]['code'])) {
            return $entity;
        }

        $codes = explode(self::DELIMITER, $itemData['categories'][0]['code']);
        foreach ($codes as $code) {
            $code = trim($code);
            $existingCategory = $this->findEntityByIdentityValues(
                Category::class,
                ['code' => $code]
            );

            if ($existingCategory instanceof Category) {
                $entity->addCategory($existingCategory);
            } else {
                $entity->clearCategories();
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

        return $entity;
    }

    private function processChannels(Product $entity, array $itemData): ?Product
    {
        if (!array_key_exists('channels', $itemData)) {
            return $entity;
        }

        $entity->clearChannels();
        if (empty($itemData['channels'][0]['code'])) {
            return $entity;
        }

        $codes = explode(self::DELIMITER, $itemData['channels'][0]['code']);
        foreach ($codes as $code) {
            $code = trim($code);
            $existingChannel = $this->findEntityByIdentityValues(
                SalesChannel::class,
                ['code' => $code]
            );

            if ($existingChannel instanceof SalesChannel) {
                $entity->addChannel($existingChannel);
            } else {
                $entity->clearChannels();
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

        return $entity;
    }

    private function processImage(Product $entity, array $itemData, ?File $newImage = null): ?Product
    {
        if (!array_key_exists('image', $itemData)) {
            return $entity;
        }

        if (!$newImage) {
            $entity->setImage(null);

            return $entity;
        }

        if (!$newImage->getFile() instanceof SymfonyFile
            || !$this->mimeTypeChecker->isImageMimeType($newImage->getFile()->getMimeType())
        ) {
            $this->processValidationErrors(
                $entity,
                [
                    $this->translator->trans(
                        'marello.product.messages.import.error.image.invalid',
                        [
                            '%url%' => $itemData['image']['externalUrl'],
                        ]
                    )
                ]
            );

            return null;
        }

        $entity->setImage($newImage);

        return $entity;
    }
}
