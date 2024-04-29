<?php

namespace Marello\Bundle\SalesBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\SalesBundle\Entity\Repository\SalesChannelGroupRepository;

#[ORM\Entity(SalesChannelGroupRepository::class), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_sales_channel_group')]
#[ORM\UniqueConstraint(name: 'UNIQ_759DCFAB3D6A9E29', columns: ['integration_channel_id'])]
#[Oro\Config(
    routeName: 'marello_sales_saleschannelgroup_index',
    routeView: 'marello_sales_saleschannelgroup_view',
    routeCreate: 'marello_sales_saleschannelgroup_create',
    routeUpdate: 'marello_sales_saleschannelgroup_update',
    defaultValues: [
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => '']
    ]
)]
class SalesChannelGroup implements DatesAwareInterface, OrganizationAwareInterface, ExtendEntityInterface
{
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;
    use DatesAwareTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['identity' => true, 'order' => 10]
        ]
    )]
    protected ?string $name = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['order' => 20]
        ]
    )]
    protected ?string $description = null;

    #[ORM\Column(name: 'is_system', type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['order' => 30]
        ]
    )]
    protected ?bool $system = false;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: SalesChannel::class, cascade: ['persist'], fetch: 'EAGER')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
        ]
    )]
    protected ?Collection $salesChannels = null;

    #[ORM\OneToOne(targetEntity: Channel::class)]
    #[ORM\JoinColumn(name: 'integration_channel_id', referencedColumnName: 'id', unique: true, nullable: true, onDelete: 'SET NULL')]
    #[Oro\ConfigField(defaultValues: [
        'dataaudit' => ['auditable' => true]
    ])]
    protected ?Channel $integrationChannel = null;

    public function __construct()
    {
        $this->salesChannels = new ArrayCollection();
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
     * @return integer
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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description = null): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSystem(): bool
    {
        return $this->system;
    }

    /**
     * @param boolean $system
     * @return $this
     */
    public function setSystem(bool $system): self
    {
        $this->system = $system;

        return $this;
    }

    /**
     * @return Collection|SalesChannel[]
     */
    public function getSalesChannels(): Collection
    {
        return $this->salesChannels;
    }

    /**
     * @param SalesChannel $salesChannel
     *
     * @return $this
     */
    public function addSalesChannel(SalesChannel $salesChannel): self
    {
        if (!$this->salesChannels->contains($salesChannel)) {
            $salesChannel->setGroup($this);
            $this->salesChannels->add($salesChannel);
        }

        return $this;
    }

    /**
     * @param SalesChannel $salesChannel
     *
     * @return $this
     */
    public function removeSalesChannel(SalesChannel $salesChannel): self
    {
        if ($this->salesChannels->contains($salesChannel)) {
            $this->salesChannels->removeElement($salesChannel);
        }

        return $this;
    }

    /**
     * @return Channel|null
     */
    public function getIntegrationChannel(): ?Channel
    {
        return $this->integrationChannel;
    }

    /**
     * @param Channel|null $integrationChannel
     * @return $this
     */
    public function setIntegrationChannel(Channel $integrationChannel = null): self
    {
        $this->integrationChannel = $integrationChannel;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}
