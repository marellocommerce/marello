<?php

namespace Marello\Bundle\CustomerBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait EmailAddressTrait
{
    #[ORM\Column(name: 'email', type: Types::STRING, nullable: false)]
    protected ?string $email = null;

    #[ORM\Column(name: 'email_lowercase', type: Types::STRING, nullable: true)]
    protected ?string $emailLowercase = null;

    /**
     * Gets an email address which can be used to send messages
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getClass(): string
    {
        return Customer::class;
    }

    /**
     * Get names of fields contain email addresses
     *
     * @return string[]|null
     */
    public function getEmailFields(): ?array
    {
        return null;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setEmailLowercase($emailLowercase = null): self
    {
        $this->emailLowercase = $emailLowercase;

        return $this;
    }

    /**
     * Gets an email address which can be used to send messages
     *
     * @return string
     */
    public function getEmailLowercase(): string
    {
        return $this->emailLowercase;
    }
}
