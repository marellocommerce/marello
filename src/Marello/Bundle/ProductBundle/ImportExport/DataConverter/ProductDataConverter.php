<?php

namespace Marello\Bundle\ProductBundle\ImportExport\DataConverter;

use Oro\Bundle\IntegrationBundle\ImportExport\DataConverter\AbstractTreeDataConverter;

class ProductDataConverter extends AbstractTreeDataConverter
{
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
            'tags:name',
        ];
    }
}
