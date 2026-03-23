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
                    'value' => 'marello_order_accepted_confirmation'
                ],
                'cancelled_email_template' => [
                    'value' => 'marello_order_cancelled'
                ],
                'payment_reminder_email_template' => [
                    'value' => 'marello_order_payment_reminder'
                ],
                'shipped_prepared_email_template' => [
                    'value' => 'marello_order_shipping_prepared'
                ],
                'shipped_confirmation_email_template' => [
                    'value' => 'marello_order_shipped_confirmation'
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
