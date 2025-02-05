<?php

namespace Marello\Bundle\ProductBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DenormalizedPropertyAwareInterface;

use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;

/**
 * Represents a Marello Variant Product
 */
#[ORM\Table(name: 'marello_product_variant')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(
    routeName: 'marello_product_index',
    routeView: 'marello_product_view',
    defaultValues: [
        'entity' => ['icon' => 'fa-barcode'],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'dataaudit' => ['auditable' => true]
    ]
)]
class Variant implements
    DenormalizedPropertyAwareInterface,
    ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use ExtendEntityTrait;
    
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
    #[ORM\Column(name: 'variant_code', type: Types::STRING, unique: true, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $variantCode;

    /**
     * @see \Marello\Bundle\InventoryBundle\Form\Type\ProductInventoryType
     *
     * @var Collection|Product[] $products
     */
    #[ORM\JoinTable(name: 'marello_product_to_variant')]
    #[ORM\OneToMany(mappedBy: 'variant', targetEntity: Product::class, cascade: ['persist'])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $products;

    /**
     * This is a mirror field for performance reasons only.
     * It mirrors getDefaultName()->getString().
     */
    #[ORM\Column(name: 'name', type: Types::STRING, length:255, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' => true]
        ],
        mode: 'hidden'
    )]
    protected ?string $denormalizedDefaultName = null;

    #[ORM\ManyToMany(targetEntity: LocalizedFallbackValue::class, cascade: ['ALL'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'marello_product_variant_name')]
    #[ORM\JoinColumn(name: 'variant_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'localized_value_id', referencedColumnName: 'id', unique: true, onDelete: 'CASCADE')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => false],
            'importexport' => ['excluded' => true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?Collection $names = null;

    #[ORM\ManyToMany(targetEntity: LocalizedFallbackValue::class, cascade: ['ALL'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'marello_product_variant_desc')]
    #[ORM\JoinColumn(name: 'variant_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'localized_value_id', referencedColumnName: 'id', unique: true, onDelete: 'CASCADE')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => false],
            'importexport' => ['excluded' => true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?Collection $descriptions = null;

    /**
     * Variant constructor.
     */
    public function __construct()
    {
        $this->names = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->descriptions = new ArrayCollection();
    }

    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
        }
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->setCreatedAt($now);
        $this->setUpdatedAt($now);

        if (!$this->getDefaultName()) {
            throw new \RuntimeException(sprintf('Variant %s has to have a default name', $this->getVariantCode()));
        }
        $this->updateDenormalizedProperties();
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));

        if (!$this->getDefaultName()) {
            throw new \RuntimeException(sprintf('Variant %s has to have a default name', $this->getSku()));
        }
        $this->updateDenormalizedProperties();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getVariantCode()
    {
        return $this->variantCode;
    }

    /**
     * @param string $variantCode
     */
    public function setVariantCode($variantCode)
    {
        $this->variantCode = $variantCode;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Add item
     *
     * @param Product $item
     *
     * @return Variant
     */
    public function addProduct(Product $item)
    {
        if (!$this->products->contains($item)) {
            $this->products->add($item);
            $item->setVariant($this);
        }

        return $this;
    }

    /**
     * Remove item
     *
     * @param Product $item
     *
     * @return Variant
     */
    public function removeProduct(Product $item)
    {
        if ($this->products->contains($item)) {
            $this->products->removeElement($item);
            $item->setVariant(null);
        }

        return $this;
    }

    /**
     * @param array|LocalizedFallbackValue[] $names
     *
     * @return $this
     */
    public function setNames(array $names = []): self
    {
        $this->names->clear();

        foreach ($names as $name) {
            $this->addName($name);
        }

        return $this;
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getNames(): Collection
    {
        return $this->names;
    }

    /**
     * @param LocalizedFallbackValue $name
     *
     * @return $this
     */
    public function addName(LocalizedFallbackValue $name): self
    {
        if (!$this->names->contains($name)) {
            $this->names->add($name);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $name
     *
     * @return $this
     */
    public function removeName(LocalizedFallbackValue $name): self
    {
        if ($this->names->contains($name)) {
            $this->names->removeElement($name);
        }

        return $this;
    }

    /**
     * @param array|LocalizedFallbackValue[] $descriptions
     *
     * @return $this
     */
    public function setDescriptions(array $descriptions = []): self
    {
        $this->descriptions->clear();

        foreach ($descriptions as $description) {
            $this->addDescription($description);
        }

        return $this;
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getDescriptions(): Collection
    {
        return $this->descriptions;
    }

    /**
     * @param LocalizedFallbackValue $description
     *
     * @return $this
     */
    public function addDescription(LocalizedFallbackValue $description): self
    {
        if (!$this->descriptions->contains($description)) {
            $this->descriptions->add($description);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $description
     *
     * @return $this
     */
    public function removeDescription(LocalizedFallbackValue $description): self
    {
        if ($this->descriptions->contains($description)) {
            $this->descriptions->removeElement($description);
        }

        return $this;
    }

    /**
     * This field is read-only, updated automatically prior to persisting.
     *
     * @return string|null
     */
    public function getDenormalizedDefaultName(): ?string
    {
        return $this->denormalizedDefaultName;
    }

    public function updateDenormalizedProperties(): void
    {
        if (!$this->getDefaultName()) {
            throw new \RuntimeException(sprintf('Variant %s has to have a default name', $this->getVariantCode()));
        }

        $this->denormalizedDefaultName = $this->getDefaultName()->getString();
    }
}
