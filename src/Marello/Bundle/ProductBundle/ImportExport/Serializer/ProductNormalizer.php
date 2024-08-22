<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Serializer;

use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ImportExportBundle\Serializer\Normalizer\ConfigurableEntityNormalizer;

class ProductNormalizer extends ConfigurableEntityNormalizer
{
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if ($data instanceof Product) {
            return true;
        }

        return parent::supportsNormalization($data, $format, $context);
    }

    protected function isFieldSkippedForNormalization($entityName, $fieldName, array $context)
    {
        if ($entityName === Product::class && $fieldName === 'image') {
            return false;
        }

        return parent::isFieldSkippedForNormalization($entityName, $fieldName, $context);
    }
}
