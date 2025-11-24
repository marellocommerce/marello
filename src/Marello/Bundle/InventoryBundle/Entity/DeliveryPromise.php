<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DenormalizedPropertyAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;

/**
  * @method LocalizedFallbackValue getDefaultLabel()
  */
#[ORM\Table(name: 'marello_inventory_delivery_promise')]
#[ORM\Entity(), ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'marello_inventory_dlvry_prom_codeorgidx', columns: ['code', 'organization_id'])]
#[Oro\Config(
    routeName: 'marello_inventory_deliverypromise_index',
    routeView: 'marello_inventory_deliverypromise_view',
    routeCreate: 'marello_inventory_deliverypromise_create',
    routeUpdate: 'marello_inventory_deliverypromise_update',
    defaultValues: [
        'entity' => ['icon' => 'fa-cubes'],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => false]
    ]
)]
class DeliveryPromise implements
    OrganizationAwareInterface,
    DenormalizedPropertyAwareInterface,
    ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;
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
     * @var string|null
     */
    #[ORM\Column(name: 'code', type: Types::STRING, nullable: false, unique: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => false]])]
    protected ?string $code = null;

    /**
     * @var Collection|null
     */
    #[ORM\ManyToMany(targetEntity: LocalizedFallbackValue::class, cascade: ['ALL'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'marello_inventory_deli_prom_label')]
    #[ORM\JoinColumn(name: 'delivery_promise_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'localized_value_id', referencedColumnName: 'id', unique: true, onDelete: 'CASCADE')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'attribute' => ['is_attribute' => true]
        ]
    )]
    protected ?Collection $labels = null;

    /**
     * This is a mirror field for performance reasons only.
     * It mirrors getDefaultLabel()->getString().
     */
    #[ORM\Column(name: 'label', type: Types::STRING, length:255, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' => true]
        ],
        mode: 'hidden'
    )]
    protected ?string $denormalizedDefaultLabel = null;

    /**
     * @var int|null
     */
    #[ORM\Column(name: 'min_days', type: Types::INTEGER, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => false]])]
    protected ?int $minDays = 0;

    /**
     * @var int|null
     */
    #[ORM\Column(name: 'max_days', type: Types::INTEGER, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => false]])]
    protected ?int $maxDays = 0;

    /**
     * @var Collection|LocalizedFallbackValue[]|null
     */
    #[ORM\ManyToMany(targetEntity: LocalizedFallbackValue::class, cascade: ['ALL'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'marello_inventory_deli_prom_tooltip')]
    #[ORM\JoinColumn(name: 'delivery_promise_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'localized_value_id', referencedColumnName: 'id', unique: true, onDelete: 'CASCADE')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => false],
            'attribute' => ['is_attribute' => true]
        ]
    )]
    protected ?Collection $tooltips = null;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->labels = new ArrayCollection();
        $this->tooltips = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->setCreatedAt($now);
        $this->setUpdatedAt($now);

        $this->updateDenormalizedProperties();
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
        $this->updateDenormalizedProperties();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @param array|LocalizedFallbackValue[] $labels
     *
     * @return $this
     */
    public function setLabels(array $labels = []): self
    {
        $this->labels->clear();

        foreach ($labels as $label) {
            $this->addLabel($label);
        }

        return $this;
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getLabels(): Collection
    {
        return $this->labels;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return $this
     */
    public function addLabel(LocalizedFallbackValue $label): self
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return $this
     */
    public function removeLabel(LocalizedFallbackValue $label): self
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
        }

        return $this;
    }

    public function updateDenormalizedProperties(): void
    {
        if (!$this->getDefaultLabel()) {
            throw new \RuntimeException('Delivery Promise need to have a default label');
        }
        $this->denormalizedDefaultLabel = $this->getDefaultLabel()->getString();
    }

    /**
     * This field is read-only, updated automatically prior to persisting.
     *
     * @return string|null
     */
    public function getDenormalizedDefaultLabel(): ?string
    {
        return $this->denormalizedDefaultLabel;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        try {
            if ($this->getDefaultLabel()) {
                return (string) $this->getDefaultLabel();
            } else {
                return (string) $this->code;
            }
        } catch (\LogicException $e) {
            return (string) $this->code;
        }
    }

    public function getMinDays(): ?int
    {
        return $this->minDays;
    }

    public function setMinDays(int $minDays = null): self
    {
        $this->minDays = $minDays;

        return $this;
    }

    public function getMaxDays(): ?int
    {
        return $this->maxDays;
    }

    public function setMaxDays(int $maxDays = null): self
    {
        $this->maxDays = $maxDays;

        return $this;
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getTooltips()
    {
        return $this->tooltips;
    }

    /**
     * @param LocalizedFallbackValue $tooltip
     *
     * @return $this
     */
    public function addTooltip(LocalizedFallbackValue $tooltip)
    {
        if (!$this->tooltips->contains($tooltip)) {
            $this->tooltips->add($tooltip);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $tooltip
     *
     * @return $this
     */
    public function removeTooltip(LocalizedFallbackValue $tooltip)
    {
        if ($this->tooltips->contains($tooltip)) {
            $this->tooltips->removeElement($tooltip);
        }

        return $this;
    }
}
