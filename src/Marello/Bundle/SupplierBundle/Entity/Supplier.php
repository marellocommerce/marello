<?php

namespace Marello\Bundle\SupplierBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EmailBundle\Model\EmailHolderInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Marello\Bundle\PricingBundle\Model\CurrencyAwareInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\SupplierBundle\Entity\Repository\SupplierRepository;

/**
 * Supplier
 */
#[ORM\Table(name: 'marello_supplier_supplier')]
#[ORM\Entity(repositoryClass: SupplierRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'marello_supplier_supplier_nameorgidx', columns: ['name', 'organization_id'])]
#[Oro\Config(
    routeName: 'marello_supplier_supplier_index',
    routeView: 'marello_supplier_supplier_view',
    defaultValues: ['security' => ['type' => 'ACL', 'group_name' => ''],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true]
    ]
)]
class Supplier implements CurrencyAwareInterface, EmailHolderInterface, ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait, AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;

    const SEND_PO_MANUALLY = 'manual';
    const SEND_PO_BY_EMAIL = 'email';

    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $name;
    
    /**
     * @var MarelloAddress
     */
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[ORM\OneToOne(targetEntity: MarelloAddress::class, cascade: ['persist', 'remove'])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $address = null;

    /**
     * @var string
     */
    #[ORM\Column(name: 'email', type: Types::STRING, length: 255, unique: true, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $email;

    /**
     * @var integer
     */
    #[ORM\Column(name: 'priority', type: Types::INTEGER, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $priority;
    
    /**
     * @var boolean
     */
    #[ORM\Column(name: 'can_dropship', type: Types::BOOLEAN, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $canDropship = true;

    /**
     * @var boolean
     */
    #[ORM\Column(name: 'is_active', type: Types::BOOLEAN, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $isActive = true;
    
    /**
     * @var string
     */
    #[ORM\Column(name: 'currency', type: Types::STRING, length: 3, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $currency;

    /**
     * @var string
     */
    #[ORM\Column(name: 'po_send_by', type: Types::STRING, length: 30, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $poSendBy;

    /**
     * @var string
     */
    #[ORM\Column(name: 'code', type: Types::STRING, nullable: true)]
    protected $code;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Supplier
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param MarelloAddress $address
     *
     * @return Supplier
     */
    public function setAddress(MarelloAddress $address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return MarelloAddress
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Supplier
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     *
     * @return Supplier
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set canDropship
     *
     * @param boolean $canDropship
     *
     * @return Supplier
     */
    public function setCanDropship($canDropship)
    {
        $this->canDropship = $canDropship;

        return $this;
    }

    /**
     * Get canDropship
     *
     * @return boolean
     */
    public function getCanDropship()
    {
        return $this->canDropship;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Supplier
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
    
    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getPoSendBy()
    {
        return $this->poSendBy;
    }

    /**
     * @param string $poSendBy
     * @return $this
     */
    public function setPoSendBy($poSendBy)
    {
        $this->poSendBy = $poSendBy;
        
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
