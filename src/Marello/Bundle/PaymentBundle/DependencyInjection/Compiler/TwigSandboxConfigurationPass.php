<?php

namespace Marello\Bundle\PaymentBundle\DependencyInjection\Compiler;

use Oro\Bundle\EmailBundle\DependencyInjection\Compiler\AbstractTwigSandboxConfigurationPass;

/**
 * Compiler pass that collects extensions for service `marello_payment.twig.payment_method_extension` and
 * `marello_payment.twig.payment_status_extension` by `marello_email.email_renderer` tag
 */
class TwigSandboxConfigurationPass extends AbstractTwigSandboxConfigurationPass
{
    /**
     * {@inheritDoc}
     */
    protected function getFunctions(): array
    {
        return [
            'marello_payment_method_enabled',
            'marello_get_payment_method_label'
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
     * {@inheritDoc}
     */
    protected function getExtensions(): array
    {
        return [
            'marello_payment.twig.payment_method_extension'
        ];
    }

    protected function getTags(): array
    {
        return [];
    }
}
