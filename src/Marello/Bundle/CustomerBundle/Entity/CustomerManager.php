<?php

namespace Marello\Bundle\CustomerBundle\Entity;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

use Oro\Bundle\UserBundle\Entity\BaseUserManager;
use Oro\Bundle\UserBundle\Entity\UserInterface;
use Oro\Bundle\UserBundle\Security\UserLoaderInterface;

use Marello\Bundle\NotificationBundle\Provider\EmailSendProcessor;

class CustomerManager extends BaseUserManager
{
    public const STATUS_ACTIVE  = 'active';
    public const STATUS_RESET = 'reset';

    private const AUTH_STATUS_ENUM_CODE = 'cu_auth_status';
    private const WELCOME_EMAIL_TEMPLATE_NAME = 'marello_customer_user_welcome_email';
    private const RESET_PASSWORD_EMAIL_TEMPLATE_NAME = 'marello_customer_user_reset_password';

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        private UserLoaderInterface $userLoader,
        private ManagerRegistry $doctrine,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private EmailSendProcessor $emailProcessor
    ) {
        parent::__construct($userLoader, $doctrine, $passwordHasherFactory);
    }

    public function sendWelcomeRegisteredByAdminEmail(Customer $user): void
    {
        $user->setConfirmationToken($user->generateToken());
        $this->emailProcessor->sendNotification(
            static::WELCOME_EMAIL_TEMPLATE_NAME,
            [$user->getEmail()],
            $user
        );
    }

    public function sendResetPasswordEmail(Customer $user): void
    {
        $user->setConfirmationToken($user->generateToken());
        $user->setPasswordRequestedAt(new \DateTime('now', new \DateTimeZone('UTC')));
        $this->emailProcessor->sendNotification(
            static::RESET_PASSWORD_EMAIL_TEMPLATE_NAME,
            [$user->getEmail()],
            $user
        );
    }

    #[\Override]
    public function findUserBy(array $criteria): ?UserInterface
    {
        return parent::findUserBy(array_merge($criteria, ['isGuest' => false]));
    }

    public function updatePassword(UserInterface $user): void
    {
        $password = $user->getPlainPassword();
        if ($password !== null && 0 !== strlen($password)) {
            $passwordHasher = $this->getPasswordHasher($user);
            $user->setPassword($passwordHasher->hash($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }
}
