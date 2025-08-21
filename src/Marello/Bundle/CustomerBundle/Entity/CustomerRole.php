<?php

namespace Marello\Bundle\CustomerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\UserBundle\Entity\AbstractRole;

/**
 * Entity that represents Customer`s roles in system
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
#[ORM\Entity]
#[ORM\Table(name: 'marello_customer_role')]
//#[ORM\UniqueConstraint(name: 'UNIQ_552B533832C8A3DE9395C3F3E', columns: ['organization_id', 'company_id', 'label'])]
#[Config(
//    routeName: 'oro_customer_customer_user_role_index',
//    routeCreate: 'oro_customer_customer_user_role_create',
//    routeUpdate: 'oro_customer_customer_user_role_update',
    defaultValues: [
        'entity' => ['icon' => 'fa-briefcase'],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ]
    ]
)]
class CustomerRole extends AbstractRole implements OrganizationAwareInterface, ExtendEntityInterface
{
    use ExtendEntityTrait;

    public const PREFIX_ROLE = 'ROLE_CUSTOMER_';

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: false)]
    #[ConfigField(defaultValues: ['importexport' => ['identity' => true]])]
    protected ?string $role = null;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(name: 'company_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?Company $company = null;

    #[ORM\ManyToOne(targetEntity: Organization::class)]
    #[ORM\JoinColumn(name: 'organization_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?OrganizationInterface $organization = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $label = null;

    /**
     * @var Collection<int, Customer>
     */
    #[ORM\ManyToMany(targetEntity: Customer::class, mappedBy: 'userRoles')]
    protected ?Collection $customers = null;

    public function __construct(string $role = '')
    {
        if ($role) {
            $this->setRole($role, false);
        }

        $this->customers = new ArrayCollection();

        parent::__construct($role ? $this->getRole() : '');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return CustomerRole
     */
    public function setLabel($label)
    {
        $this->label = (string)$label;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrefix()
    {
        return static::PREFIX_ROLE;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company|null $company
     * @return CustomerRole
     */
    public function setCompany(Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrganization(OrganizationInterface $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPredefined()
    {
        return !$this->getCompany();
    }

    public function __clone()
    {
        $this->id = null;
        $this->cloneExtendEntityStorage();
    }

    /**
     * Clones this role and resets some of its properties that should not be shared between this and new role.
     *
     * @return static
     */
    public function duplicate()
    {
        $newRole = clone $this;
        $newRole->setRole($newRole->getLabel());
        $newRole->customers = new ArrayCollection();

        return $newRole;
    }

    /**
     * @param Customer $customer
     *
     * @return $this
     */
    public function addCustomer(Customer $customer)
    {
        if (!$this->customers->contains($customer)) {
            $this->customers[] = $customer;
        }

        return $this;
    }

    /**
     * @param Customer $customerUser
     *
     * @return $this
     */
    public function removeCustomer(Customer $customer)
    {
        $this->customers->removeElement($customer);

        return $this;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    public function __serialize(): array
    {
        return [
            $this->id,
            $this->role,
            $this->label,
            $this->organization
        ];
    }

    public function __unserialize(array $serialized): void
    {
        [
            $this->id,
            $this->role,
            $this->label,
            $this->organization
        ] = $serialized;

        $this->customers = new ArrayCollection();
    }
}
