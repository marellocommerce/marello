<?php

namespace Marello\Bundle\AddressBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\AddressBundle\Entity\AddressType;
use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

#[ORM\Entity, ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_typed_address')]
#[ORM\UniqueConstraint(name: 'marello_typed_addressidx', columns: ['address_id'])]
#[Oro\Config(
    defaultValues: [
        'dataaudit' => [
            'auditable' => true
        ],
        'security' => [
            'type' => 'ACL',
            'group_name' => ''
        ]
    ]
)]
class MarelloTypedAddress extends AbstractAddress implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    #[ORM\ManyToOne(targetEntity: AddressType::class)]
    #[ORM\JoinColumn(name: 'address_type', referencedColumnName: 'name', onDelete: 'SET NULL')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
        ]
    )]
    protected ?AddressType $addressType = null;

    #[ORM\Column(name: 'is_default', type: Types::BOOLEAN, options: ['default' => 0])]
    protected ?bool $isDefault = false;

    #[ORM\Column(name: 'phone', type: Types::STRING, length: 32, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $phone = null;

    #[ORM\Column(name: 'company', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $company = null;


    public function getFullName()
    {
        return implode(' ', array_filter([
            $this->namePrefix,
            $this->firstName,
            $this->middleName,
            $this->lastName,
            $this->nameSuffix,
        ]));
    }

    #[ORM\PreUpdate]
    public function preUpdateTimestamp()
    {
        $this->updated = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    #[ORM\PrePersist]
    public function prePersistTimestamp()
    {
        $this->created = $this->updated = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * @return AddressType|null
     */
    public function getAddressType(): ?AddressType
    {
        return $this->addressType;
    }

    /**
     * @param AddressType|null $addressType
     * @return $this
     */
    public function setAddressType(?AddressType $addressType): self
    {
        $this->addressType = $addressType;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool|null $isDefault
     * @return $this
     */
    public function setIsDefault(?bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }
}
