<?php

namespace Marello\Bundle\CatalogBundle\Api\Processor;

use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Oro\Bundle\ApiBundle\Processor\Create\CreateContext;
use Oro\Bundle\ApiBundle\Exception\RuntimeException;

class ValidateCategoryDataProcessor implements ProcessorInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContextInterface $context): void
    {
        /** @var CreateContext $context */
        $data = $context->getRequestData();

        if (!isset($data['data']) || !is_array($data['data'])) {
            throw new RuntimeException('The request must contain a "data" section.');
        }

        $resource = $data['data'];

        if ($resource['attributes']['categoryType'] == 'customer') {
            if (empty($resource['relationships']['customer']) || $resource['relationships']['customer']['data'] === null) {
                throw new RuntimeException('Customer relationship is required for "customer" category type.');
            }
        }
    }
}