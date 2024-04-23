<?php

namespace Marello\Bundle\CustomerBundle\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait EmailAddressTrait
{
    #[ORM\Column(name: 'email', type: Types::STRING, nullable: false)]
    protected ?string $email = null;

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
}
