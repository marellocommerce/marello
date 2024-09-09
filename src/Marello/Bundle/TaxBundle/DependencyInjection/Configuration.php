<?php

namespace Marello\Bundle\TaxBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    const SYSTEM_TAX_ORIGIN_COUNTRY_CONFIG_PATH = 'marello_tax.origin_country';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(MarelloTaxExtension::ALIAS);
        $rootNode = $treeBuilder->getRootNode();

        SettingsBuilder::append(
            $rootNode,
            [
                'origin_country' => [
                    'value' => null
                ]
            ]
        );

        return $treeBuilder;
    }
}
