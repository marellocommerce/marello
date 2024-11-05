<?php

namespace Marello\Bundle\TaxBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Marello\Bundle\TaxBundle\DependencyInjection\Compiler\TaxMapperPass;
use Marello\Bundle\TaxBundle\DependencyInjection\Compiler\TaxRuleMatcherPass;
use Marello\Bundle\TaxBundle\DependencyInjection\Compiler\ResolverEventConnectorPass;

class MarelloTaxBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TaxRuleMatcherPass());
        $container->addCompilerPass(new TaxMapperPass());
        $container->addCompilerPass(new ResolverEventConnectorPass());
        parent::build($container);
    }
}
