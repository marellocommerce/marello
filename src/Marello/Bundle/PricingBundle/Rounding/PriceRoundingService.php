<?php

namespace Marello\Bundle\PricingBundle\Rounding;

use Oro\Bundle\CurrencyBundle\Rounding\AbstractRoundingService;
use Marello\Bundle\PricingBundle\DependencyInjection\Configuration;

class PriceRoundingService extends AbstractRoundingService
{
    /** {@inheritdoc} */
    public function getRoundType()
    {
        return $this->configManager->get(Configuration::PRICING_ROUNDING_TYPE);
    }

    /** {@inheritdoc} */
    public function getPrecision()
    {
        return $this->configManager->get(Configuration::PRICING_PRECISION);
    }
}
