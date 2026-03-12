<?php

namespace Marello\Bundle\ReturnBundle\DependencyInjection;

use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('marello_return');
        $rootNode = $treeBuilder->getRootNode();

        SettingsBuilder::append(
            $rootNode,
            [
                'ror_period'          => ['value' => 30],
                'warranty_period'     => ['value' => 24],
                'created_template'    => ['value' => null],
                'not_verified_template'    => ['value' => null],
                'received_template'    => ['value' => null],
                'inspection_not_ok_template'    => ['value' => null],
                'shipping_label_warehouse_template'    => ['value' => null],
                'reminder_template'    => ['value' => null]
            ]
        );

        return $treeBuilder;
    }
}
