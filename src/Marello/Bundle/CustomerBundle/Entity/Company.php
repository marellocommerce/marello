<?php

namespace Marello\Bundle\CustomerBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Marello\Bundle\PaymentTermBundle\Entity\PaymentTerm;
use Marello\Bundle\CustomerBundle\Entity\Repository\CompanyRepository;

#[ORM\Entity(CompanyRepository::class), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_customer_company')]
#[ORM\UniqueConstraint(name: 'marello_customer_company_compnrorgidx', columns: ['company_number', 'organization_id'])]
#[Oro\Config(
    routeName: 'marello_customer_company_index',
    routeView: 'marello_customer_company_view',
    routeCreate: 'marello_customer_company_create',
    routeUpdate: 'marello_customer_company_update',
    defaultValues: [
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'grid' => ['default' => 'marello-companies-grid']
    ]
)]
class Company implements DatesAwareInterface, OrganizationAwareInterface, ExtendEntityInterface
{
    use DatesAwareTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['identity' => true, 'order' => 10]])]
    protected ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 20]]
    )]
    protected ?string $name = null;

    #[ORM\Column(name: 'company_number', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 35]]
    )]
    protected ?string $companyNumber = null;

    #[ORM\ManyToOne(targetEntity: PaymentTerm::class)]
    #[ORM\JoinColumn(name: 'payment_term_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['order' => 40]])]
    protected ?PaymentTerm $paymentTerm = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: ['children'])]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Oro\ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true],
        'importexport' => ['header' => 'Parent', 'order' => 45]
    ])]
    protected ?Company $parent = null;

    #[ORM\OneToMany(mappedBy: ['parent'], targetEntity: Company::class)]
    #[Oro\ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true],
        'importexport' => ['excluded' => true]
    ])]
    protected ?Collection $children = null;

    #[ORM\ManyToMany(targetEntity: MarelloAddress::class, cascade: ['persist'], fetch: 'EAGER')]
    #[ORM\JoinTable(name: 'marello_company_join_address')]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'address_id', referencedColumnName: 'id',  unique: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => [
                'auditable' => true
            ]
        ]
    )]
    protected ?Collection $addresses = null;

    #[ORM\OneToMany(mappedBy: ['company'], targetEntity: Customer::class, cascade: ['persist'])]
    #[Oro\ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true],
        'importexport' => ['excluded' => true]
    ])]
    protected ?Collection $customers = null;

    #[ORM\Column(name: 'tax_identification_number', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true]]
    )]
    protected ?string $taxIdentificationNumber = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->customers = new ArrayCollection();
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
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getName();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getCompanyNumber(): ?string
    {
        return $this->companyNumber;
    }

    /**
     * @param string|null $companyNumber
     * @return $this
     */
    public function setCompanyNumber(string $companyNumber = null): self
    {
        $this->companyNumber = $companyNumber;

        return $this;
    }

    /**
     * @return PaymentTerm
     */
    public function getPaymentTerm(): ?PaymentTerm
    {
        return $this->paymentTerm;
    }

    /**
     * @param PaymentTerm $paymentTerm
     *
     * @return $this
     */
    public function setPaymentTerm(PaymentTerm $paymentTerm = null): self
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }

    /**
     * @param Company $parent
     *
     * @return $this
     */
    public function setParent(Company $parent = null): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Company
     */
    public function getParent(): ?Company
    {
        return $this->parent;
    }

    /**
     * @param MarelloAddress $address
     *
     * @return $this
     */
    public function addAddress(MarelloAddress $address): self
    {
        if (!$this->getAddresses()->contains($address)) {
            $this->getAddresses()->add($address);
        }

        return $this;
    }

    /**
     * @param MarelloAddress $address
     *
     * @return $this
     */
    public function removeAddress(MarelloAddress $address): self
    {
        if ($this->hasAddress($address)) {
            $this->getAddresses()->removeElement($address);
        }

        return $this;
    }

    /**
     * @return Collection|MarelloAddress[]
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    /**
     * @param MarelloAddress $address
     *
     * @return bool
     */
    protected function hasAddress(MarelloAddress $address): bool
    {
        return $this->getAddresses()->contains($address);
    }

    /**
     * @param Company $child
     *
     * @return $this
     */
    public function addChild(Company $child): self
    {
        if (!$this->hasChild($child)) {
            $child->setParent($this);
            $this->children->add($child);
        }

        return $this;
    }

    /**
     * @param Company $child
     *
     * @return $this
     */
    public function removeChild(Company $child): self
    {
        if ($this->hasChild($child)) {
            $child->setParent(null);
            $this->children->removeElement($child);
        }

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    /**
     * @param Company $child
     *
     * @return bool
     */
    protected function hasChild(Company $child): bool
    {
        return $this->children->contains($child);
    }

    /**
     * @param Customer $customer
     *
     * @return $this
     */
    public function addCustomer(Customer $customer): self
    {
        if (!$this->hasCustomer($customer)) {
            $customer->setCompany($this);
            $this->customers->add($customer);
        }

        return $this;
    }

    /**
     * @param Customer $customer
     *
     * @return $this
     */
    public function removeCustomer(Customer $customer): self
    {
        if ($this->hasCustomer($customer)) {
            $customer->setCompany(null);
            $this->customers->removeElement($customer);
        }

        return $this;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    /**
     * @param Customer $customer
     *
     * @return bool
     */
    protected function hasCustomer(Customer $customer): bool
    {
        return $this->customers->contains($customer);
    }

    /**
     * @return string
     */
    public function getTaxIdentificationNumber(): ?string
    {
        return $this->taxIdentificationNumber;
    }

    /**
     * @param string $taxIdentificationNumber
     *
     * @return $this
     */
    public function setTaxIdentificationNumber(string $taxIdentificationNumber = null): self
    {
        $this->taxIdentificationNumber = $taxIdentificationNumber;

        return $this;
    }
}
