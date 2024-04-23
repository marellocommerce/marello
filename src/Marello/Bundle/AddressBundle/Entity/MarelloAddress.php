<?php

namespace Marello\Bundle\AddressBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\AddressBundle\Entity\AbstractAddress;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\AddressBundle\Entity\Repository\MarelloAddressRepository;

#[ORM\Entity(MarelloAddressRepository::class), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_address')]
#[ORM\AssociationOverrides([
    new ORM\AssociationOverride(
        name: 'region',
        joinColumns: [new ORM\JoinColumn(name: 'region_code', referencedColumnName: 'combined_code', nullable: true)]
    )
])]
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
class MarelloAddress extends AbstractAddress implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    #[ORM\Column(name: 'phone', type: Types::STRING, length: 32, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $phone = null;

    #[ORM\Column(name: 'company', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $company = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, cascade: ['persist'], inversedBy: 'addresses')]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?Customer $customer = null;

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
    }

    /**
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     *
     * @return $this
     */
    public function setCustomer(Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

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
}
