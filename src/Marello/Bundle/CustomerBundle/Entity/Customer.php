<?php

namespace Marello\Bundle\CustomerBundle\Entity;


use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\LocaleBundle\Model\FullNameInterface;
use Oro\Bundle\EmailBundle\Entity\EmailOwnerInterface;
use Oro\Bundle\EmailBundle\Model\EmailHolderInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
#[ORM\Entity, ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_customer_customer')]
#[ORM\UniqueConstraint(name: 'marello_customer_emailorgidx', columns: ['email', 'organization_id'])]
#[Oro\Config(
    routeName: 'marello_customer_index',
    routeView: 'marello_customer_view',
    routeCreate: 'marello_customer_create',
    routeUpdate: 'marello_customer_update',
    defaultValues: [
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'grid' => ['default' => 'marello-customer-select-grid']
    ]
)]
class Customer implements
    FullNameInterface,
    EmailHolderInterface,
    EmailOwnerInterface,
    DatesAwareInterface,
    OrganizationAwareInterface,
    ExtendEntityInterface
{
    use FullNameTrait, EmailAddressTrait;
    use DatesAwareTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['identity' => true, 'order' => 10]])]
    protected ?int $id = null;

    #[ORM\OneToOne(targetEntity: MarelloAddress::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'primary_address_id', referencedColumnName: 'id', nullable: true)]
    #[Oro\ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true]
    ])]
    protected ?MarelloAddress $primaryAddress = null;

    #[ORM\OneToOne(targetEntity: MarelloAddress::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'shipping_address_id', referencedColumnName: 'id', nullable: true)]
    #[Oro\ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true]
    ])]
    protected ?MarelloAddress $shippingAddress = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: MarelloAddress::class, cascade: ['persist'])]
    #[Oro\ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true]
    ])]
    protected ?Collection $addresses = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'customers')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Oro\ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true],
        'importexport' => ['full' => true, 'order' => 45]
    ])]
    protected ?Company $company = null;

    /**
     * Customer constructor.
     */
    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    /**
     * @param string  $firstName
     * @param string  $lastName
     * @param string  $email
     * @param MarelloAddress $primaryAddress
     * @param MarelloAddress $shippingAddress
     *
     * @return Customer
     */
    public static function create(
        $firstName,
        $lastName,
        $email,
        MarelloAddress $primaryAddress,
        MarelloAddress $shippingAddress = null
    ): self {
        $customer = new self();

        $customer
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email)
            ->setPrimaryAddress($primaryAddress)
        ;

        if ($shippingAddress) {
            $customer->setShippingAddress($shippingAddress);
        }

        return $customer;
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->setCreatedAt($now);
        $this->setUpdatedAt($now);
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
    }

    /**
     * Get entity unique id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection|\Oro\Bundle\AddressBundle\Entity\AbstractAddress[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    /**
     * @param MarelloAddress $address
     *
     * @return $this
     */
    public function addAddress(MarelloAddress $address): self
    {
        $this->addresses->add($address->setCustomer($this));

        return $this;
    }

    /**
     * @param MarelloAddress $address
     *
     * @return $this
     */
    public function removeAddress(MarelloAddress $address): self
    {
        $this->addresses->removeElement($address->setCustomer(null));

        return $this;
    }

    /**
     * @return MarelloAddress
     */
    public function getPrimaryAddress(): ?MarelloAddress
    {
        return $this->primaryAddress;
    }

    /**
     * @param MarelloAddress $primaryAddress
     *
     * @return $this
     */
    public function setPrimaryAddress(MarelloAddress $primaryAddress = null): self
    {
        $primaryAddress->setCustomer($this);
        $this->primaryAddress = $primaryAddress;

        return $this;
    }

    /**
     * @param MarelloAddress $shippingAddress
     *
     * @return $this
     */
    public function setShippingAddress(MarelloAddress $shippingAddress = null): self
    {
        $shippingAddress->setCustomer($this);
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * @return MarelloAddress
     */
    public function getShippingAddress(): ?MarelloAddress
    {
        return $this->shippingAddress;
    }

    /**
     * @return Company
     */
    public function getCompany(): ?Company
    {
        return $this->company;
    }

    /**
     * @param Company $company
     * @return $this
     */
    public function setCompany(Company $company = null): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFullName();
    }
}
