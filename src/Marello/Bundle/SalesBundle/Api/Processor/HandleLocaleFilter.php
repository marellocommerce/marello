<?php

namespace Marello\Bundle\SalesBundle\Api\Processor;

use Oro\Bundle\ApiBundle\Processor\Context;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;

class HandleLocaleFilter implements ProcessorInterface
{
    /**
     * @param ContextInterface $context
     * @return void
     */
    public function process(ContextInterface $context): void
    {
        /** @var Context $context */
        $filterValues = $context->getFilterValues();
        $sharedData = $context->getSharedData();

        if ($filterValues->has('filter[locale]')) {
            $locale = $filterValues->get('filter[locale]');
            $sharedData->set('locale', $locale->getValue());
            $context->setSharedData($sharedData);
        }
    }
}