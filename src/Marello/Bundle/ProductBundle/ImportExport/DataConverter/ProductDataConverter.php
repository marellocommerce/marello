<?php

namespace Marello\Bundle\ProductBundle\ImportExport\DataConverter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\ImportExport\Helper\ProductAttributesHelper;
use Oro\Bundle\EntityBundle\Helper\FieldHelper;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeFamily;
use Oro\Bundle\IntegrationBundle\ImportExport\DataConverter\AbstractTreeDataConverter;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductDataConverter extends AbstractTreeDataConverter
{
    /**
     * @var FieldHelper
     */
    protected $fieldHelper;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ProductAttributesHelper
     */
    protected $productAttributesHelper;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    protected array $additionalAttributes = [];

    public function setFieldHelper(FieldHelper $fieldHelper): void
    {
        $this->fieldHelper = $fieldHelper;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    public function setProductAttributesHelper(ProductAttributesHelper $productAttributesHelper): void
    {
        $this->productAttributesHelper = $productAttributesHelper;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function convertToExportFormat(array $exportedRecord, $skipNullValues = true)
    {
        if (!empty($exportedRecord['attributeFamily']['code'])) {
            $attributeFamily = $this->entityManager
                ->getRepository(AttributeFamily::class)
                ->findOneBy(['code' => $exportedRecord['attributeFamily']['code']]);

            $attributes = $this->productAttributesHelper->getAttributesForExport($attributeFamily);
            foreach ($attributes as $attribute) {
                $label = $this->fieldHelper->getConfigValue(
                    Product::class,
                    $attribute->getFieldName(),
                    'header',
                    $this->translator->trans($attribute->toArray('entity')['label'])
                );
                $this->additionalAttributes[$label] = $attribute->getFieldName();
            }
        }

        $result = parent::convertToExportFormat($exportedRecord, $skipNullValues);
        $this->additionalAttributes = [];

        return $result;
    }

    protected function getHeaderConversionRules()
    {
        return array_merge([
            'Name' => 'names:default:string',
            'SKU' => 'sku',
            'Status' => 'status:name',
            'Attribute Family' => 'attributeFamily:code',
            'Tax Code' => 'taxCode:code',
            'Sales Channels' => 'channels:0:code',
            'Categories' => 'categories:0:code',
            'Image' => 'image:externalUrl',
            'Tags' => 'tags:name',
        ], $this->additionalAttributes);
    }

    protected function getBackendHeader()
    {
        return array_merge([
            'names:default:string',
            'sku',
            'status:name',
            'attributeFamily:code',
            'taxCode:code',
            'channels:0:code',
            'categories:0:code',
            'image:externalUrl',
            'tags:name',
        ], array_values($this->additionalAttributes));
    }

    protected function convertHeaderToBackend(array $frontendHeader)
    {
        $result = parent::convertHeaderToBackend($frontendHeader);

        $fields = $this->fieldHelper->getEntityFields(Product::class);
        foreach ($fields as $i => $field) {
            $fields[$i]['importHeader'] = $this->fieldHelper->getConfigValue(
                Product::class,
                $field['name'],
                'header',
                $field['label']
            );
        }

        foreach ($result as $key => $value) {
            if ($key !== $value) {
                continue;
            }

            // If after converting the field is the same - then replace value to an internal field name
            foreach ($fields as $field) {
                if ($field['importHeader'] !== $key) {
                    continue;
                }

                $result[$key] = $field['name'];
            }
        }

        return $result;
    }
}
