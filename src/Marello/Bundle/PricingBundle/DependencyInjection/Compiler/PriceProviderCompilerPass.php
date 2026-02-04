<?php

namespace Marello\Bundle\PricingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class PriceProviderCompilerPass implements CompilerPassInterface
{
    const TAG = 'marello_pricing.company_price_provider';
    const SERVICE = 'Marello\Bundle\PricingBundle\Provider\CompanyPriceProviderRegistry';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::SERVICE)) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds(self::TAG);
        if (empty($taggedServices)) {
            return;
        }

        $registryDefinition = $container->getDefinition(self::SERVICE);

        foreach ($taggedServices as $strategy => $value) {
            $registryDefinition->addMethodCall('addPriceProvider', [new Reference($strategy)]);
        }
    }
}
