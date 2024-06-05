<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Configuration;

use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfiguration;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfigurationInterface;
use Oro\Bundle\ImportExportBundle\Configuration\ImportExportConfigurationProviderInterface;

class ProductConfiguration implements ImportExportConfigurationProviderInterface
{
    public function get(): ImportExportConfigurationInterface
    {
        return new ImportExportConfiguration([
            ImportExportConfiguration::FIELD_ENTITY_CLASS => Product::class,
            ImportExportConfiguration::FIELD_EXPORT_TEMPLATE_PROCESSOR_ALIAS => 'marello_product',
            ImportExportConfiguration::FIELD_IMPORT_PROCESSOR_ALIAS => 'marello_product',
            ImportExportConfiguration::FIELD_IMPORT_ENTITY_LABEL
                => 'marello.product.messages.import.tab_label.product',
            ImportExportConfiguration::FIELD_EXPORT_TEMPLATE_BUTTON_LABEL
                => 'marello.product.messages.export.template.product.label',
        ]);
    }
}
