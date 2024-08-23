<?php

namespace Marello\Bundle\ProductBundle\ImportExport\DataConverter;

use Oro\Bundle\IntegrationBundle\ImportExport\DataConverter\AbstractTreeDataConverter;

class AssembledChannelPriceListDataConverter extends AbstractTreeDataConverter
{
    protected function getHeaderConversionRules()
    {
        return [
            'Product SKU' => 'product:sku',
            'Channel' => 'channel:code',
            'Currency' => 'currency',
            'Default Price' => 'defaultPrice:value',
            'Special Price' => 'specialPrice:value',
            'Special Price Start Date' => 'specialPrice:startDate',
            'Special Price End Date' => 'specialPrice:endDate',
        ];
    }

    protected function getBackendHeader()
    {
        return [
            'product:sku',
            'channel:code',
            'currency',
            'defaultPrice:value',
            'specialPrice:value',
            'specialPrice:startDate',
            'specialPrice:endDate',
        ];
    }
}
