<?php

namespace Marello\Bundle\SalesBundle\DependencyInjection\CompilerPass;

use Marello\Bundle\SalesBundle\Config\SalesChannelScopeManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SalesChannelApiConfigScopePass implements CompilerPassInterface
{
    private const PARAM = 'oro_config.api.scopes';

    public function process(ContainerBuilder $container)
    {
        $param = $container->getParameter(self::PARAM);
        $param[] = SalesChannelScopeManager::SCOPE_NAME;
        $container->setParameter(self::PARAM, $param);
    }
}
