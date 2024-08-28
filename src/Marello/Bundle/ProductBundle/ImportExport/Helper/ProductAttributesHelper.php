<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Helper;

use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeFamily;
use Oro\Bundle\EntityConfigBundle\Entity\FieldConfigModel;
use Oro\Bundle\EntityConfigBundle\Manager\AttributeManager;

class ProductAttributesHelper
{
    private const EXCLUDED_DEFAULT_ATTRIBUTES = ['sku', 'names', 'status'];

    public function __construct(
        private AttributeManager $attrbuteManager
    ) {
    }

    /**
     * @param AttributeFamily $attributeFamily
     * @return FieldConfigModel[]
     */
    public function getAttributesForExport(AttributeFamily $attributeFamily): array
    {
        $attributes = $this->attrbuteManager->getAttributesByFamily($attributeFamily);

        $result = [];
        foreach ($attributes as $attribute) {
            if (\in_array($attribute->getFieldName(), self::EXCLUDED_DEFAULT_ATTRIBUTES)) {
                continue;
            }

            // Skip if the type is not applicable
            if (!array_key_exists($attribute->getType(), $this->getPlaceholders())) {
                continue;
            }

            $result[] = $attribute;
        }

        return $result;
    }

    public function getPlaceholders(): array
    {
        return [
            'boolean' => true,
            'integer' => 42,
            'bigint' => 42,
            'float' => 42.05,
            'string' => 'some_string',
            'text' => 'some_text',
            'datetime' => new \DateTime(),
        ];
    }
}
