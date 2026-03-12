<?php

namespace Marello\Bundle\OrderBundle\DependencyInjection;

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
        $treeBuilder = new TreeBuilder('marello_order');
        $rootNode = $treeBuilder->getRootNode();

        SettingsBuilder::append(
            $rootNode,
            [
                'confirmation_email_template' => [
                    'value' => null
                ],
                'cancelled_email_template' => [
                    'value' => null
                ],
                'payment_reminder_email_template' => [
                    'value' => null
                ],
                'shipped_prepared_email_template' => [
                    'value' => null
                ],
                'shipped_confirmation_email_template' => [
                    'value' => null
                ],
                'order_on_demand_enabled' => [
                    'value' => false
                ],
                'order_on_demand' => [
                    'value' => false
                ],
            ]
        );

        return $treeBuilder;
    }
}
