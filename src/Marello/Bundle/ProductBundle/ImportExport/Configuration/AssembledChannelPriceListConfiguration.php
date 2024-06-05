<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Configuration;

use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfiguration;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfigurationInterface;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfigurationProviderInterface;

class AssembledChannelPriceListConfiguration implements ImportExportConfigurationProviderInterface
{
    public function get(): ImportExportConfigurationInterface
    {
        return new ImportExportConfiguration([
            ImportExportConfiguration::FIELD_ENTITY_CLASS => AssembledChannelPriceList::class,
            ImportExportConfiguration::FIELD_EXPORT_TEMPLATE_PROCESSOR_ALIAS => 'marello_product_channel_price',
            ImportExportConfiguration::FIELD_IMPORT_PROCESSOR_ALIAS => 'marello_product_channel_price',
            ImportExportConfiguration::FIELD_IMPORT_ENTITY_LABEL
                => 'marello.product.messages.import.tab_label.assembled_channel_price_list',
            ImportExportConfiguration::FIELD_EXPORT_TEMPLATE_BUTTON_LABEL
                => 'marello.product.messages.export.template.assembled_channel_price_list.label',
        ]);
    }
}
