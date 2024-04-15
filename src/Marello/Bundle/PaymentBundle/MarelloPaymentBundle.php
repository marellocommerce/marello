<?php

namespace Marello\Bundle\PaymentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Marello\Bundle\PaymentBundle\DependencyInjection\Compiler\CompositePaymentMethodProviderCompilerPass;

class MarelloPaymentBundle extends Bundle
{
    /** {@inheritdoc} */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CompositePaymentMethodProviderCompilerPass());

        parent::build($container);
    }
}

