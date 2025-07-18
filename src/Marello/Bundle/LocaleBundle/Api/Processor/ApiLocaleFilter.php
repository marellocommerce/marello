<?php

namespace Marello\Bundle\LocaleBundle\Api\Processor;

use Oro\Bundle\ApiBundle\Processor\Context;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;

class ApiLocaleFilter implements ProcessorInterface
{
    const LOCALE_FILTER = 'filter[locale]';
    /**
     * @param ContextInterface $context
     * @return void
     */
    public function process(ContextInterface $context): void
    {
        /** @var Context $context */
        $filterValues = $context->getFilterValues();
        $sharedData = $context->getSharedData();

        if ($filterValues->has(self::LOCALE_FILTER)) {
            $locale = $filterValues->get(self::LOCALE_FILTER);
            $sharedData->set('locale', $locale->getValue());
            $context->setSharedData($sharedData);

            // just remove the filter value to prevent issue with locale not being a field on the entity.
            $filterValues->remove(self::LOCALE_FILTER);
            $context->setFilterValues($filterValues);
            $filters = $context->getFilters();
            if ($filters->has(self::LOCALE_FILTER)) {
                $filters->remove(self::LOCALE_FILTER);
            }
        }
    }
}
