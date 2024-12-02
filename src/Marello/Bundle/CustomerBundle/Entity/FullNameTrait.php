<?php

namespace Marello\Bundle\CustomerBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait FullNameTrait
{

    #[ORM\Column(name: 'name_prefix', type: Types::STRING, nullable: true)]
    protected ?string $namePrefix = null;

    #[ORM\Column(name: 'first_name', type: Types::STRING, nullable: false)]
    protected ?string $firstName = null;

    #[ORM\Column(name: 'middle_name', type: Types::STRING, nullable: true)]
    protected ?string $middleName = null;

    #[ORM\Column(name: 'last_name', type: Types::STRING, nullable: false)]
    protected ?string $lastName = null;

    #[ORM\Column(name: 'name_suffix', type: Types::STRING, nullable: true)]
    protected ?string $nameSuffix = null;

    /**
     * @return string
     */
    public function getNamePrefix(): ?string
    {
        return $this->namePrefix;
    }

    /**
     * @param string $namePrefix
     *
     * @return $this
     */
    public function setNamePrefix(string $namePrefix = null): self
    {
        $this->namePrefix = $namePrefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * @param mixed $middleName
     *
     * @return $this
     */
    public function setMiddleName(string $middleName = null): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     *
     * @return $this
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNameSuffix(): ?string
    {
        return $this->nameSuffix;
    }

    /**
     * @param mixed $nameSuffix
     *
     * @return $this
     */
    public function setNameSuffix(string $nameSuffix = null): self
    {
        $this->nameSuffix = $nameSuffix;

        return $this;
    }

    /**
     * Returns all names concatenated into full name.
     *
     * @return string
     */
    public function getFullName(): string
    {
        $names = array_filter([
            $this->namePrefix,
            $this->firstName,
            $this->middleName,
            $this->lastName,
            $this->nameSuffix,
        ]);

        return implode(' ', $names);
    }
}
