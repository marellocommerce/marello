<?php

namespace Marello\Bundle\DigitalAssetBundle;

use Marello\Bundle\DigitalAssetBundle\DependencyInjection\Compiler\DigitalAssetCategoryExtendGuesserPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MarelloDigitalAssetBundle extends Bundle
{
//    public function build(ContainerBuilder $container): void
//    {
//        parent::build($container);
//        $container->addCompilerPass(new DigitalAssetCategoryExtendGuesserPass());
//    }
}