<?php

<<<<<<<< HEAD:package/marello/src/Marello/Bundle/POSBundle/DependencyInjection/MarelloPOSExtension.php
namespace Marello\Bundle\POSBundle\DependencyInjection;
========
namespace Marello\Bundle\ImportExportBundle\DependencyInjection;
>>>>>>>> develop:package/marello/src/Marello/Bundle/ImportExportBundle/DependencyInjection/MarelloImportExportExtension.php

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

<<<<<<<< HEAD:package/marello/src/Marello/Bundle/POSBundle/DependencyInjection/MarelloPOSExtension.php
class MarelloPOSExtension extends Extension
========
class MarelloImportExportExtension extends Extension
>>>>>>>> develop:package/marello/src/Marello/Bundle/ImportExportBundle/DependencyInjection/MarelloImportExportExtension.php
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('eventlisteners.yml');
    }
}
