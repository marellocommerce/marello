<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Configuration;

use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfiguration;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfigurationInterface;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfigurationProviderInterface;

class AssembledPriceListConfiguration implements ImportExportConfigurationProviderInterface
{
    public function get(): ImportExportConfigurationInterface
    {
        return new ImportExportConfiguration([
            ImportExportConfiguration::FIELD_ENTITY_CLASS => AssembledPriceList::class,
            ImportExportConfiguration::FIELD_EXPORT_TEMPLATE_PROCESSOR_ALIAS => 'marello_product_price',
            ImportExportConfiguration::FIELD_IMPORT_PROCESSOR_ALIAS => 'marello_product_price',
            ImportExportConfiguration::FIELD_IMPORT_ENTITY_LABEL
                => 'marello.product.messages.import.tab_label.assembled_price_list',
            ImportExportConfiguration::FIELD_EXPORT_TEMPLATE_BUTTON_LABEL
                => 'marello.product.messages.export.template.assembled_price_list.label',
        ]);
    }
}
