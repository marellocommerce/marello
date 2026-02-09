<?php

namespace Marello\Bundle\PaymentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Marello\Bundle\PaymentBundle\DependencyInjection\Compiler\CompositePaymentMethodProviderCompilerPass;
use Marello\Bundle\PaymentBundle\DependencyInjection\Compiler\TwigSandboxConfigurationPass;

class MarelloPaymentBundle extends Bundle
{
    /** {@inheritdoc} */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompositePaymentMethodProviderCompilerPass());
        $container->addCompilerPass(new TwigSandboxConfigurationPass());

        parent::build($container);
    }
}
