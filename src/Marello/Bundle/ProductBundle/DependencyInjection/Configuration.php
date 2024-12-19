<?php

namespace Marello\Bundle\ProductBundle\DependencyInjection;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public const ROOT_NODE = 'marello_product';
    public const USE_EXTERNAL_URL_CONFIG = 'use_external_url';
    public const DEFAULT_MAX_NUMBER_OF_RELATED_ITEMS_COUNT = 25;

    public const RELATED_PRODUCTS_BIDIRECTIONAL = 'related_products_bidirectional';
    public const MAX_NUMBER_OF_RELATED_PRODUCTS = 'max_number_of_related_products';
    public const MAX_NUMBER_OF_UPSELL_PRODUCTS = 'max_number_of_upsell_products';
    public const MAX_NUMBER_OF_CROSSSELL_PRODUCTS = 'max_number_of_crosssell_products';


    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::ROOT_NODE);
        $rootNode = $treeBuilder->getRootNode();

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        SettingsBuilder::append(
            $rootNode,
            [
                self::USE_EXTERNAL_URL_CONFIG => ['value' => false],
                self::RELATED_PRODUCTS_BIDIRECTIONAL => ['value' => false],
                self::MAX_NUMBER_OF_RELATED_PRODUCTS => [
                    'value' => self::DEFAULT_MAX_NUMBER_OF_RELATED_ITEMS_COUNT
                ],
                self::MAX_NUMBER_OF_UPSELL_PRODUCTS => [
                    'value' => self::DEFAULT_MAX_NUMBER_OF_RELATED_ITEMS_COUNT
                ],
                self::MAX_NUMBER_OF_CROSSSELL_PRODUCTS => [
                    'value' => self::DEFAULT_MAX_NUMBER_OF_RELATED_ITEMS_COUNT
                ],
            ]
        );

        return $treeBuilder;
    }

    public static function getConfigKeyByName(string $name): string
    {
        return self::ROOT_NODE . ConfigManager::SECTION_MODEL_SEPARATOR . $name;
    }
}
