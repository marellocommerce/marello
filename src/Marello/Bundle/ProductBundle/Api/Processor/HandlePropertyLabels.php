<?php

namespace Marello\Bundle\ProductBundle\Api\Processor;

use Oro\Bundle\ApiBundle\Processor\Context;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;

use Marello\Bundle\ProductBundle\Entity\Product;

class HandlePropertyLabels implements ProcessorInterface
{
    public const OPERATION_NAME = 'handle_property_labels';
    public const LABEL_CONFIG_FIELD = 'label';

    /**
     * @param ContextInterface $context
     * @return void
     */
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

    /**
     * @param array $data
     * @param array $labels
     * @return array
     */
    protected function updateCollectionData(array $data, array $labels): array
    {
        foreach ($data as &$list) {
            foreach ($list as &$item) {
                if (is_array($item)) {
                    foreach ($labels as $key => $label) {
                        if (is_array($item['attributes']) && array_key_exists($key, $item['attributes'])) {
                            $labeledAttribute = [$label => $item['attributes'][$key]];
                            unset($item['attributes'][$key]);
                            $item['attributes'] = array_merge($item['attributes'], $labeledAttribute);
                        }

                        if (is_array($item['relationships']) && array_key_exists($key, $item['relationships'])) {
                            $labeledAttribute = [$label => $item['relationships'][$key]];
                            unset($item['relationships'][$key]);
                            $item['relationships'] = array_merge($item['relationships'], $labeledAttribute);
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param array $data
     * @param array $labels
     * @return array
     */
    protected function updateSingleItemData(array $data, array $labels): array
    {
        foreach ($data as &$item) {
            if (is_array($item)) {
                foreach ($labels as $key => $label) {
                    if (is_array($item['attributes']) && array_key_exists($key, $item['attributes'])) {
                        $labeledAttribute = [$label => $item['attributes'][$key]];
                        unset($item['attributes'][$key]);
                        $item['attributes'] = array_merge($item['attributes'], $labeledAttribute);
                    }
                }
            }
        }

        return $data;
    }
}
