<?php

namespace Marello\Bundle\CatalogBundle;

use Oro\Bundle\LocaleBundle\DependencyInjection\Compiler\EntityFallbackFieldsStoragePass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MarelloCatalogBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        
        $container
            ->addCompilerPass(new EntityFallbackFieldsStoragePass([
                'Marello\Bundle\CatalogBundle\Entity\Category' => [
                    'name' => 'names',
                    'description' => 'descriptions'
                ]
            ]));
    }
}
