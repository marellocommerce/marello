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
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\LocaleBundle\Model\LocalizationTrait;
use Marello\Bundle\PricingBundle\Model\CurrencyAwareInterface;
use Marello\Bundle\LocaleBundle\Model\LocalizationAwareInterface;
use Marello\Bundle\SalesBundle\Entity\Repository\SalesChannelRepository;

#[ORM\Entity(SalesChannelRepository::class), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_sales_sales_channel')]
#[ORM\UniqueConstraint(name: 'marello_sales_sales_channel_codeidx', columns: ['code'])]
#[Oro\Config(
    routeName: 'marello_sales_saleschannel_index',
    routeView: 'marello_sales_saleschannel_view',
    routeCreate: 'marello_sales_saleschannel_create',
    routeUpdate: 'marello_sales_saleschannel_update',
    defaultValues: [
        'entity' => [
            'icon' => 'fa-sitemap',
        ],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => '']
    ]
)]
class SalesChannel implements
    CurrencyAwareInterface,
    LocalizationAwareInterface,
    ExtendEntityInterface,
    DatesAwareInterface,
    OrganizationInterface
{
    use DatesAwareTrait;
    use LocalizationTrait;
    use ExtendEntityTrait;
    use AuditableOrganizationAwareTrait;
    
    const DEFAULT_TYPE = 'marello';

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?string $name = null;

    #[ORM\Column(name: 'code', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['identity' => true]
        ]
    )]
    protected ?string $code = null;

    #[ORM\Column(name: 'currency', type: Types::STRING, length: 5, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['identity' => true]
        ]
    )]
    protected ?string $currency = null;

    #[ORM\Column(name: 'active', type: Types::BOOLEAN, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?bool $active = true;

    #[ORM\Column(name: 'is_default', type: Types::BOOLEAN, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?bool $default = true;

    #[ORM\ManyToOne(targetEntity: SalesChannelType::class)]
    #[ORM\JoinColumn(name: 'channel_type', referencedColumnName: 'name', nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?SalesChannelType $channelType = null;

    #[ORM\ManyToOne(targetEntity: SalesChannelGroup::class, inversedBy: 'salesChannels')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?SalesChannelGroup $group = null;

    #[ORM\OneToOne(targetEntity: Channel::class)]
    #[ORM\JoinColumn(name: 'integration_channel_id', nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?Channel $integrationChannel = null;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'channels')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?Collection $products = null;

    #[ORM\ManyToOne(targetEntity: SalesChannel::class)]
    #[ORM\JoinColumn(name: 'associated_sales_channel_id', referencedColumnName: 'id', nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected ?SalesChannel $associatedSalesChannel = null;

    /**
     * @param string|null $name
     */
    public function __construct($name = null)
    {
        $this->name = $name;
        $this->products = new ArrayCollection();
    }

    public function __clone()
    {
        if ($this->id) {
            $this->id                   = null;
            $this->products = new ArrayCollection();
        }
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
     * @return int
     */
    public function getId(): int
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
    public function __toString(): string
    {
        return $this->name;
    }
    
    /**
     * @return SalesChannelType
     */
    public function getChannelType(): SalesChannelType
    {
        return $this->channelType;
    }

    /**
     * @param SalesChannelType $channelType
     *
     * @return $this
     */
    public function setChannelType(SalesChannelType $channelType): self
    {
        $this->channelType = $channelType;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getDefault(): bool
    {
        return $this->default;
    }

    /**
     * @param boolean $default
     *
     * @return $this
     */
    public function setDefault(bool $default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @return SalesChannelGroup
     */
    public function getGroup(): SalesChannelGroup
    {
        return $this->group;
    }

    /**
     * @param SalesChannelGroup $group
     * @return $this
     */
    public function setGroup(SalesChannelGroup $group): self
    {
        $this->group = $group;

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
     * @param Channel $integrationChannel
     * @return $this
     */
    public function setIntegrationChannel(Channel $integrationChannel = null): self
    {
        $this->integrationChannel = $integrationChannel;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasProducts(): bool
    {
        return count($this->products) > 0;
    }

    /**
     * @param Product $product
     * @return bool
     */
    public function hasProduct(Product $product): bool
    {
        return $this->products->contains($product);
    }

    /**
     * @return ArrayCollection|SalesChannel|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function getAssociatedSalesChannel(): ?SalesChannel
    {
        return $this->associatedSalesChannel;
    }

    public function setAssociatedSalesChannel(?SalesChannel $associatedSalesChannel = null): self
    {
        $this->associatedSalesChannel = $associatedSalesChannel;

        return $this;
    }
}
