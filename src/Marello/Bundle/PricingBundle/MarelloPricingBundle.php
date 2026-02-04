<?php

namespace Marello\Bundle\PricingBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Marello\Bundle\PricingBundle\DependencyInjection\Compiler\SubtotalProviderPass;
use Marello\Bundle\PricingBundle\DependencyInjection\Compiler\PriceProviderCompilerPass;

class MarelloPricingBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SubtotalProviderPass());
        $container->addCompilerPass(new PriceProviderCompilerPass());
        parent::build($container);
    }
}
