<?php

namespace Marello\Bundle\ProductBundle\Api\Processor;

use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ApiBundle\Processor\GetConfig\ConfigContext;
use Oro\Bundle\EntityConfigBundle\Config\ConfigManager;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;

class HandleLabelForFieldConfig implements ProcessorInterface
{
    public function __construct(
        private ConfigManager $configManager
    ) {}

    public function process(ContextInterface $context): void
    {
        /** @var ConfigContext $context */
        $entityClass = $context->getClassName();
        if ($entityClass !== Product::class) {
            return;
        }

        $definition = $context->getResult();
        foreach ($definition->getFields() as $fieldName => $fieldConfig) {
            if (!$this->configManager->hasConfig($entityClass, $fieldName)) {
                continue;
            }
            $attributeFieldConfig = $this->configManager->getFieldConfig(
                'attribute',
                $entityClass,
                $fieldName
            );
            if (!$attributeFieldConfig->get('is_attribute')) {
                continue;
            }
            $label = $attributeFieldConfig->get('field_name');
            if (!$label) {
                continue;
            }

            $fieldConfig->set(HandlePropertyLabels::LABEL_CONFIG_FIELD, $label);
        }
    }
}
