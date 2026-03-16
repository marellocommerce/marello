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
                'created_template'    => ['value' => 'marello_return_created'],
                'not_verified_template'    => ['value' => 'marello_return_not_verified'],
                'received_template'    => ['value' => 'marello_return_received'],
                'inspection_not_ok_template'    => ['value' => 'marello_return_inspection_not_ok'],
                'reminder_template'    => ['value' => 'marello_return_reminder']
            ]
        );

        return $treeBuilder;
    }
}
