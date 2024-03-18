<?php

namespace Marello\Bundle\ProductBundle\Api\Processor;

use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ApiBundle\Processor\Context;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;

class HandlePropertyLabels implements ProcessorInterface
{
    public const OPERATION_NAME = 'handle_property_labels';
    public const LABEL_CONFIG_FIELD = 'label';

    public function process(ContextInterface $context): void
    {
        /** @var Context $context */
        $entityClass = $context->getClassName();
        if ($entityClass !== Product::class) {
            return;
        }

        $data = $context->getResult();
        if (!\is_array($data) || empty($data)) {
            return;
        }

        $config = $context->getConfig();
        if (null === $config) {
            return;
        }

        $labels = [];
        foreach ($config->getFields() as $key => $fieldConfig) {
            if ($fieldConfig->has(self::LABEL_CONFIG_FIELD)) {
                $labels[$key] = $fieldConfig->get(self::LABEL_CONFIG_FIELD);
            }
        }

        if ($context->get('action') === 'get_list') {
            $result = $this->updateCollectionData($data, $labels);
        } else {
            $result = $this->updateSingleItemData($data, $labels);
        }
        $context->setResult($result);
    }

    protected function updateCollectionData(array $data, array $labels): array
    {
        foreach ($data as &$list) {
            foreach ($list as &$item) {
                foreach ($labels as $key => $label) {
                    if (array_key_exists($key, $item['attributes'])) {
                        $labeledAttribute = [$label => $item['attributes'][$key]];
                        unset($item['attributes'][$key]);
                        $item['attributes'] = array_merge($item['attributes'], $labeledAttribute);
                    }
                }
            }
        }

        return $data;
    }

    protected function updateSingleItemData(array $data, array $labels): array
    {
        foreach ($data as &$item) {
            foreach ($labels as $key => $label) {
                if (array_key_exists($key, $item['attributes'])) {
                    $labeledAttribute = [$label => $item['attributes'][$key]];
                    unset($item['attributes'][$key]);
                    $item['attributes'] = array_merge($item['attributes'], $labeledAttribute);
                }
            }
        }

        return $data;
    }
}
