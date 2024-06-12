<?php

namespace Marello\Bundle\POSBundle\Api\Processor;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Oro\Bundle\ApiBundle\Processor\Create\CreateContext;
use Oro\Bundle\UserBundle\Exception\BadCredentialsException;
use Oro\Bundle\SecurityBundle\Authentication\Guesser\OrganizationGuesserInterface;

use Marello\Bundle\POSBundle\Api\Model\Login;

/**
 * Checks whether the login credentials are valid,
 * and if so, sets API access key of authenticated pos user to the model.
 */
class HandleLogin implements ProcessorInterface
{
    /**
     * @param string $firewallName
     * @param AuthenticatorInterface $authenticator
     * @param UserProviderInterface $userProvider
     * @param OrganizationGuesserInterface $organizationGuesser
     * @param UserCheckerInterface $userChecker
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        private string $firewallName,
        private AuthenticatorInterface $authenticator,
        private UserProviderInterface $userProvider,
        private OrganizationGuesserInterface $organizationGuesser,
        private UserCheckerInterface $userChecker,
        private EventDispatcher $eventDispatcher,
        private TranslatorInterface $translator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        /** @var CreateContext $context */
        $model = $context->getResult();
        if (!$model instanceof Login) {
            // the request is already handled
            return;
        }

        $token = $this->authenticate($model);
        $authenticatedUser = $token->getUser();
        if (!$authenticatedUser instanceof User) {
            throw new AccessDeniedException('The login via API is not supported for this user.');
        }

        $apiKey = $this->getApiKey($authenticatedUser);
        $model->setRoles($token->getRoleNames());
        $model->setApiKey($apiKey);
    }

    /**
     * @param Login $model
     *
     * @return TokenInterface
     */
    private function authenticate(Login $model)
    {
        $passport = new Passport(
            new UserBadge($model->getUser(), [$this->userProvider, 'loadUserByIdentifier']),
            new PasswordCredentials($model->getPassword()),
        );
        try {
            $user = $passport->getUser();
            $this->userChecker->checkPreAuth($user);
            $organization = $this->organizationGuesser->guess($user);
            $passport->setAttribute('organization', $organization);
            // check the passport (e.g. password checking)
            $event = new CheckPassportEvent($this->authenticator, $passport);
            $this->eventDispatcher->dispatch($event);
            $this->userChecker->checkPostAuth($user);

            return $this->authenticator->createToken($passport, $this->firewallName);
        } catch (AuthenticationException $exception) {
            $exception = new BadCredentialsException('Bad credentials.', 0, $exception);
            $exception->setMessageKey('Invalid credentials.');

            throw new AccessDeniedException(sprintf(
                'The user authentication fails. Reason: %s',
                $this->translator->trans($exception->getMessageKey(), $exception->getMessageData(), 'security')
            ));
        }
    }

    /**
     * @param User $user
     *
     * @return string|null
     */
    private function getApiKey(User $user)
    {
        $apiKey = $user->getApiKeys()->first();
        if (!$apiKey) {
            return null;
        }

        return $apiKey->getApiKey();
    }
}
