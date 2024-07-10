<?php

namespace Marello\Bundle\ProductBundle\ImportExport\DataConverter;

use Oro\Bundle\IntegrationBundle\ImportExport\DataConverter\AbstractTreeDataConverter;

class AssembledPriceListDataConverter extends AbstractTreeDataConverter
{
    protected function getHeaderConversionRules()
    {
        return [
            'Product SKU' => 'product:sku',
            'Currency' => 'currency',
            'Default Price' => 'defaultPrice:value',
            'Special Price' => 'specialPrice:value',
            'Special Price Start Date' => 'specialPrice:startDate',
            'Special Price End Date' => 'specialPrice:endDate',
            'MSRP Price' => 'msrpPrice:value',
        ];
    }

    protected function getBackendHeader()
    {
        return [
            'product:sku',
            'currency',
            'defaultPrice:value',
            'specialPrice:value',
            'specialPrice:startDate',
            'specialPrice:endDate',
            'msrpPrice:value',
        ];
    }
}
