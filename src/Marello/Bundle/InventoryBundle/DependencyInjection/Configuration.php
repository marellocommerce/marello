<?php

namespace Marello\Bundle\InventoryBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;

use Marello\Bundle\InventoryBundle\Strategy\EqualDivision\EqualDivisionBalancerStrategy;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see
 * {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public const SYSTEM_CONFIG_PATH_BALANCE_STRATEGY = 'marello_inventory.balancing_strategy';
    public const SYSTEM_CONFIG_PATH_THRESHOLD_PERCENTAGE = 'marello_inventory.balance_threshold_percentage';
    public const SYSTEM_CONFIG_PATH_MANAGED_INVENTORY_FOR_EXTERNAL_WAREHOUSE =
        'marello_inventory.managed_inventory_for_external_warehouse';
    public const SYSTEM_CONFIG_PATH_ADJUST_INVENTORY_QTY = 'marello_inventory.adjust_inventory_qty';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(MarelloInventoryExtension::ALIAS);
        $rootNode = $treeBuilder->getRootNode();

        SettingsBuilder::append(
            $rootNode,
            [
                'balancing_strategy' => [
                    'value' => EqualDivisionBalancerStrategy::IDENTIFIER
                ],
                'balance_threshold_percentage' => [
                    'value' => 0.20
                ],
                'inventory_allocation_priority' => [
                    'value' => 0
                ],
                'managed_inventory_for_external_warehouse' => [
                    'value' => false,
                ],
                'adjust_inventory_qty' => [
                    'value' => true,
                ],
            ]
        );

        return $treeBuilder;
    }
}
