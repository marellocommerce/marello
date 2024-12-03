<?php

namespace Marello\Bundle\TicketBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\LocaleBundle\Model\FullNameInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;

#[ORM\Entity(), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_ticket_ticket')]
#[Oro\Config(
    routeName: 'marello_ticket_ticket_index',
    defaultValues: [
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => '']
    ]
)]
class Ticket implements
    ExtendEntityInterface,
    FullNameInterface
{
    use EntityCreatedUpdatedAtTrait;
    use ExtendEntityTrait;

    /**
     * @var int|null
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected ?int $id = null;

    /**
     * @var Customer|null
     */
    #[ORM\ManyToOne(targetEntity: Customer::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?Customer $customer = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'name_prefix', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $namePrefix = null;

    /**
     * @var string
     */
    #[ORM\Column(name: 'first_name', type: Types::STRING, length: 255, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $firstName = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'middle_name', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $middleName = null;

    /**
     * @var string
     */
    #[ORM\Column(name: 'last_name', type: Types::STRING, length: 255, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $lastName = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'name_suffix', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $nameSuffix = null;

    /**
     * @var string
     */
    #[ORM\Column(name: 'email', type: Types::STRING, length: 255, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $email = null;

    /**
     * @var string|null
     */
    #[ORM\Column(name: 'phone', type: Types::STRING, length: 255, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $phone = null;

    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?User $owner = null;

    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'assigned_to_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?User $assignedTo = null;

    /**
     * @var \Extend\Entity\EV_Marello_Ticket_Priority
     * @Assert\NotNull
     */
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?\Extend\Entity\EV_Marello_Ticket_Priority $ticketPriority = null;

    /**
     * @var \Extend\Entity\EV_Marello_Ticket_Source
     */
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?\Extend\Entity\EV_Marello_Ticket_Source $ticketSource = null;

    /**
     * @var \Extend\Entity\EV_Marello_Ticket_Status
     * @Assert\NotNull
     */
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?\Extend\Entity\EV_Marello_Ticket_Status $ticketStatus = null;

    /**
     * @var TicketCategory
     */
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: TicketCategory::class, cascade: ['persist'])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?TicketCategory $category = null;

    /**
     * @var string
     */
    #[ORM\Column(name: 'subject', type: Types::STRING, length: 255, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $subject = null;

    /**
     * @var string
     */
    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $description = null;

    /**
     * @var string
     */
    #[ORM\Column(name: 'resolution', type: Types::TEXT, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected ?string $resolution = null;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return $this
     */
    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer|null $customer
     * @return $this
     */
    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getResolution(): ?string
    {
        return $this->resolution;
    }

    /**
     * @param string|null $resolution
     * @return $this
     */
    public function setResolution(?string $resolution): self
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     * @return $this
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     * @return $this
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return $this
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User|null $owner
     * @return $this
     */
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return TicketCategory|null
     */
    public function getCategory(): ?TicketCategory
    {
        return $this->category;
    }

    /**
     * @param TicketCategory|null $category
     * @return $this
     */
    public function setCategory(?TicketCategory $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    /**
     * @param User|null $assignedTo
     * @return $this
     */
    public function setAssignedTo(?User $assignedTo): self
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    /**
     * @return \Extend\Entity\EV_Marello_Ticket_Status|null
     */
    public function getTicketStatus()
    {
        return $this->ticketStatus;
    }

    /**
     * @param \Extend\Entity\EV_Marello_Ticket_Status|null $ticketStatus
     * @return $this
     */
    public function setTicketStatus($ticketStatus): self
    {
        $this->ticketStatus = $ticketStatus;

        return $this;
    }

    /**
     * @return \Extend\Entity\EV_Marello_Ticket_Source
     */
    public function getTicketSource()
    {
        return $this->ticketSource;
    }

    /**
     * @param \Extend\Entity\EV_Marello_Ticket_Source|null $ticketSource
     * @return $this
     */
    public function setTicketSource($ticketSource): self
    {
        $this->ticketSource = $ticketSource;

        return $this;
    }

    /**
     * @return \Extend\Entity\EV_Marello_Ticket_Priority|null
     */
    public function getTicketPriority()
    {
        return $this->ticketPriority;
    }

    /**
     * @param \Extend\Entity\EV_Marello_Ticket_Priority|null $ticketPriority
     * @return $this
     */
    public function setTicketPriority($ticketPriority): self
    {
        $this->ticketPriority = $ticketPriority;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * @param string|null $middleName
     * @return $this
     */
    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNamePrefix(): ?string
    {
        return $this->namePrefix;
    }

    /**
     * @param string|null $namePrefix
     * @return $this
     */
    public function setNamePrefix(?string $namePrefix): self
    {
        $this->namePrefix = $namePrefix;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNameSuffix(): ?string
    {
        return $this->nameSuffix;
    }

    /**
     * @param string|null $nameSuffix
     * @return $this
     */
    public function setNameSuffix(?string $nameSuffix): self
    {
        $this->nameSuffix = $nameSuffix;

        return $this;
    }
}
