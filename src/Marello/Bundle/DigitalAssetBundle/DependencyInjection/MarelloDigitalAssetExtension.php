<?php

namespace Marello\Bundle\DigitalAssetBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class MarelloDigitalAssetExtension extends Extension
{
    const ALIAS = 'marello_digital_asset';

    #[\Override]
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        // register services configuration
        $loader->load('services.yml');
        $loader->load('controllers.yml');
        $loader->load('form.yml');
        $loader->load('mq_topics.yml');
        // register other configurations in the same way
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return self::ALIAS;
    }
}