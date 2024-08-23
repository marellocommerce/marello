<?php

namespace Marello\Bundle\ProductBundle\ImportExport\DataConverter;

use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\EntityBundle\Helper\FieldHelper;
use Oro\Bundle\IntegrationBundle\ImportExport\DataConverter\AbstractTreeDataConverter;

class ProductDataConverter extends AbstractTreeDataConverter
{
    /**
     * @var FieldHelper
     */
    protected $fieldHelper;

    public function setFieldHelper(FieldHelper $fieldHelper): void
    {
        $this->fieldHelper = $fieldHelper;
    }

    protected function getHeaderConversionRules()
    {
        return [
            'Name' => 'names:default:string',
            'SKU' => 'sku',
            'Status' => 'status:name',
            'Attribute Family' => 'attributeFamily:code',
            'Tax Code' => 'taxCode:code',
            'Sales Channels' => 'channels:0:code',
            'Categories' => 'categories:0:code',
            'Image' => 'image:externalUrl',
            'Tags' => 'tags:name',
        ];
    }

    protected function getBackendHeader()
    {
        return [
            'names:default:string',
            'sku',
            'status:name',
            'attributeFamily:code',
            'taxCode:code',
            'channels:0:code',
            'categories:0:code',
            'image:externalUrl',
            'tags:name',
        ];
    }

    protected function convertHeaderToBackend(array $frontendHeader)
    {
        $result = parent::convertHeaderToBackend($frontendHeader);

        $fields = $this->fieldHelper->getEntityFields(Product::class);
        foreach ($fields as &$field) {
            $field['importHeader'] = $this->fieldHelper->getConfigValue(
                Product::class,
                $field['name'],
                'header',
                $field['label']
            );
        }
        unset($field);

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
