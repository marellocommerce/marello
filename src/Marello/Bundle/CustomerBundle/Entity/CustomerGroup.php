<?php

namespace Marello\Bundle\CustomerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;


/**
 * @ORM\Entity()
 * @ORM\Table(name="marello_customer_group")
 * @Config(
 *      routeName="marello_customer_group_index",
 *      defaultValues={
 *          "security"={
 *              "type"="ACL",
 *              "group_name"=""
 *           },
 *           "grid"={
 *                "default"="marello-customer-group-grid"
 *           },
 *           "dataaudit"={
 *                "auditable"=true
 *           }
 *       }
 *  )
 *
 * @ORM\HasLifecycleCallbacks()
 */
class CustomerGroup implements ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use ExtendEntityTrait;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(
     *     name="name",
     *     type="string",
     *     length=255,
     *     nullable=false
     * )
     */
    protected $name;

    /**
     * @var Collection|Customer[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Marello\Bundle\CustomerBundle\Entity\Customer",
     *     mappedBy="customerGroup"
     * )
     * @ConfigField(
     *       defaultValues={
     *           "dataaudit"={
     *               "auditable"=true
     *           },
     *           "importexport"={
     *               "excluded"=true
     *           }
     *       }
     *  )
     */
    private $customers;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return Collection|Customer[]
     */
    public function getCustomers()
    {
        return $this->customers;
    }

    /**
     * @param Customer $customer
     *
     * @return bool
     */
    protected function hasCustomer(Customer $customer)
    {
        return $this->customers->contains($customer);
    }

    /**
     * @param Customer $customer
     *
     * @return $this
     */
    public function addCustomer(Customer $customer)
    {
        if (!$this->hasCustomer($customer)) {
            $customer->setCustomerGroup($this);
            $this->customers->add($customer);
        }

        return $this;
    }

    /**
     * @param Customer $customer
     *
     * @return $this
     */
    public function removeCustomer(Customer $customer)
    {
        if ($this->hasCustomer($customer)) {
            $customer->setCustomerGroup(null);
            $this->customers->removeElement($customer);
        }

        return $this;
    }
}