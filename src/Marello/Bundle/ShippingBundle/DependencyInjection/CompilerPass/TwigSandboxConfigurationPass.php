<?php

namespace Marello\Bundle\ShippingBundle\DependencyInjection\CompilerPass;

use Oro\Bundle\EmailBundle\DependencyInjection\Compiler\AbstractTwigSandboxConfigurationPass;

class TwigSandboxConfigurationPass extends AbstractTwigSandboxConfigurationPass
{
    /**
     * {@inheritDoc}
     */
    protected function getFunctions(): array
    {
        return [
            'marello_shipping_method_with_type_label'
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function getFilters(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTags(): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtensions(): array
    {
        return [
            'marello_shipping.twig.shipping_method_extension'
        ];
    }
}
