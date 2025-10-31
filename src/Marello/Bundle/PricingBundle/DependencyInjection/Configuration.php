<?php

namespace Marello\Bundle\PricingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Oro\Bundle\CurrencyBundle\Rounding\PriceRoundingService;
use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;

use Marello\Bundle\PricingBundle\Provider\CompanyPriceProvider;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    const VAT_SYSTEM_CONFIG_PATH = 'marello_pricing.is_vat_included';
    const PRICING_ROUNDING_TYPE = 'marello_pricing.rounding_type';
    const PRICING_PRECISION = 'marello_pricing.precision';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(MarelloPricingExtension::ALIAS);
        $rootNode = $treeBuilder->getRootNode();

        SettingsBuilder::append(
            $rootNode,
            [
                'is_vat_included' => [
                    'value' => false
                ],
                'rounding_type' => ['value' => PriceRoundingService::ROUND_HALF_UP],
                'precision' => ['value' => PriceRoundingService::DEFAULT_PRECISION],
                'pricing_provider' => ['value' => CompanyPriceProvider::PROVIDER_IDENTIFIER]
            ]
        );

        return $treeBuilder;
    }
}
