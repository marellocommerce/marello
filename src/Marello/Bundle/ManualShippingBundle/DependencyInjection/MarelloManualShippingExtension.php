<?php

namespace Marello\Bundle\ManualShippingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MarelloManualShippingExtension extends Extension
{
    /** @internal */
    const ALIAS = 'marello_manual_shipping';

    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('form_types.yml');
        $loader->load('event_listeners.yml');
        $loader->load('factories.yml');
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return static::ALIAS;
    }
}
