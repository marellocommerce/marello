<?php

namespace Marello\Bundle\NotificationBundle\Provider;

use Oro\Bundle\AttachmentBundle\Entity\Attachment;
use Symfony\Contracts\Translation\TranslatorInterface;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityBundle\Twig\Sandbox\SystemVariablesProviderInterface;

/**
 * Provides the following system variables for email templates:
 * * emailLogoUrl
 */
class SystemVariablesProvider implements SystemVariablesProviderInterface
{
    const EMAIL_LOGO_PLACEHOLDER = 'bundles/marellocore/img/marello-logo.png';

    public function __construct(
        protected TranslatorInterface $translator,
        protected ConfigManager $configManager,
        protected DoctrineHelper $doctrineHelper
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
            /** @var Attachment $attachment */
            $attachment = $this->doctrineHelper->getEntityRepositoryForClass(Attachment::class)->find($val);
            $mediaUrl = $attachment->getFile()->getMediaUrl();

            $val = sprintf('%s/%s', $this->configManager->get('oro_ui.application_url'), self::EMAIL_LOGO_PLACEHOLDER);
            if ($mediaUrl) {
                $val = sprintf('%s/%s', $this->configManager->get('oro_ui.application_url'), $mediaUrl);
            }
        } else {
            $val = [
                'type'  => 'string',
                'label' => $this->translator->trans('marello.notification.config.fields.general.logo.label')
            ];
        }
        $result['emailLogoUrl'] = $val;
    }
}
