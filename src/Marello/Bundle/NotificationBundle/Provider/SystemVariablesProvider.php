<?php

namespace Marello\Bundle\NotificationBundle\Provider;

use Symfony\Contracts\Translation\TranslatorInterface;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityBundle\Twig\Sandbox\SystemVariablesProviderInterface;

/**
 * Provides the following system variables for email templates:
 * * emailLogoUrl
 */
class SystemVariablesProvider implements SystemVariablesProviderInterface
{
    public function __construct(
        protected TranslatorInterface $translator,
        protected ConfigManager $configManager
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableDefinitions(): array
    {
        return $this->getVariables(false);
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableValues(): array
    {
        return $this->getVariables(true);
    }

    /**
     * @param bool $addValue FALSE for variable definitions; TRUE for variable values
     *
     * @return array
     */
    private function getVariables(bool $addValue): array
    {
        $result = [];

        $this->addApplicationUrl($result, $addValue);

        return $result;
    }

    private function addApplicationUrl(array &$result, bool $addValue): void
    {
        if ($addValue) {
            $val = $this->configManager->get('marello_notification.email_logo');
        } else {
            $val = [
                'type'  => 'string',
                'label' => $this->translator->trans('marello.notification.config.fields.general.logo.label')
            ];
        }
        $result['emailLogoUrl'] = $val;
    }
}
