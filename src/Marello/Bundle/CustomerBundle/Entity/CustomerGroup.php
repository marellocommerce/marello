<?php

namespace Marello\Bundle\CustomerBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;

#[ORM\Entity(), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_customer_group')]
#[Oro\Config(
    routeName: 'marello_customer_group_index',
    routeView: 'marello_customer_group_view',
    defaultValues: [
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'grid' => ['default' => 'marello-customer-group-grid']
    ]
)]
class CustomerGroup implements ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
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

    #[ORM\OneToMany(mappedBy: 'customerGroup', targetEntity: Customer::class, cascade: ['persist'])]
    #[Oro\ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true],
        'importexport' => ['excluded' => true]
    ])]
    protected ?Collection $customers = null;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection
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
     * @param Customer $customer
     *
     * @return $this
     */
    public function addCustomer(Customer $customer): self
    {
        if (!$this->hasCustomer($customer)) {
            $this->customers->add($customer);
            $customer->setCustomerGroup($this);
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
            $this->customers->removeElement($customer);
            $customer->setCustomerGroup(null);
        }

        return $this;
    }
}
