<?php

namespace Marello\Bundle\SalesBundle;

use Marello\Bundle\SalesBundle\DependencyInjection\CompilerPass\SalesChannelApiConfigScopePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MarelloSalesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SalesChannelApiConfigScopePass());
    }
}
