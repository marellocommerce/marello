<?php

namespace Marello\Bundle\UPSBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Marello\Bundle\UPSBundle\DependencyInjection\CompilerPass\UPSRequestFactoriesCompilerPass;

class MarelloUPSBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new UPSRequestFactoriesCompilerPass());
    }
}
