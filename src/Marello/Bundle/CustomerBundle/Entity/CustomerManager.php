<?php

namespace Marello\Bundle\CustomerBundle\Entity;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

use Oro\Bundle\UserBundle\Entity\UserInterface;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\UserBundle\Entity\BaseUserManager;
use Oro\Bundle\UserBundle\Security\UserLoaderInterface;

use Marello\Bundle\NotificationBundle\Provider\EmailSendProcessor;

class CustomerManager extends BaseUserManager
{
    private const WELCOME_EMAIL_TEMPLATE_NAME = 'marello_customer.marello_customer_user_welcome_email';
    private const RESET_PASSWORD_EMAIL_TEMPLATE_NAME = 'marello_customer.marello_customer_user_reset_password';

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        private UserLoaderInterface $userLoader,
        private ManagerRegistry $doctrine,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private EmailSendProcessor $emailProcessor,
        private ConfigManager $configManager
    ) {
        parent::__construct($userLoader, $doctrine, $passwordHasherFactory);
    }

    public function sendWelcomeRegisteredByAdminEmail(Customer $user): void
    {
        $user->setConfirmationToken($user->generateToken());
        $emailTemplate = $this->configManager->get(
            static::WELCOME_EMAIL_TEMPLATE_NAME,
            false,
            false,
            $user->getOrganization()
        );
        $this->emailProcessor->sendNotification(
            $emailTemplate,
            [$user->getEmail()],
            $user
        );
    }

    public function sendResetPasswordEmail(Customer $user): void
    {
        $user->setConfirmationToken($user->generateToken());
        $user->setPasswordRequestedAt(new \DateTime('now', new \DateTimeZone('UTC')));
        $userData = $user->getData();
        $scope = null;
        if (isset($userData['salesChannel'])) {
            $scope = $userData['salesChannel'];
        }
        $emailTemplate = $this->configManager->get(
            static::RESET_PASSWORD_EMAIL_TEMPLATE_NAME,
            false,
            false,
            $scope ?? $user->getOrganization()
        );
        $this->emailProcessor->sendNotification(
            $emailTemplate,
            [$user->getEmail()],
            $user
        );
    }
}
