<?php

namespace Marello\Bundle\CatalogBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DenormalizedPropertyAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\CatalogBundle\Entity\Repository\CategoryRepository;

#[ORM\Entity(CategoryRepository::class), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_catalog_category')]
#[ORM\UniqueConstraint(name: 'marello_catalog_category_codeorgidx', columns: ['code', 'organization_id'])]
#[Oro\Config(
    routeName: 'marello_category_index',
    routeView: 'marello_category_view',
    routeUpdate: 'marello_category_update',
    defaultValues: [
        'entity' => [
            'icon' => 'fa-folder',
        ],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => '', 'category' => 'catalog']
    ]
)]
class Category implements 
    DatesAwareInterface, 
    OrganizationAwareInterface, 
    ExtendEntityInterface,
    DenormalizedPropertyAwareInterface
{
    use DatesAwareTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected ?int $id = null;

    /**
     * This is a mirror field for performance reasons only.
     * It mirrors getDefaultName()->getString().
     */
    #[ORM\Column(name: 'name', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' => true]
        ],
        mode: 'hidden'
    )]
    protected ?string $denormalizedDefaultName = null;

    #[ORM\ManyToMany(targetEntity: LocalizedFallbackValue::class, cascade: ['ALL'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'marello_catalog_category_name')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'localized_value_id', referencedColumnName: 'id', unique: true, onDelete: 'CASCADE')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['order' => 20, 'full' => true, 'fallback_field' => 'string'],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?Collection $names = null;

    #[ORM\Column(name: 'code', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['identity' => true]]
    )]
    protected ?string $code = null;

    #[ORM\ManyToMany(targetEntity: LocalizedFallbackValue::class, cascade: ['ALL'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'marello_catalog_category_desc')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'localized_value_id', referencedColumnName: 'id', unique: true, onDelete: 'CASCADE')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['order' => 30, 'full' => true, 'fallback_field' => 'text'],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?Collection $descriptions = null;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'categories')]
    #[ORM\JoinTable(name: 'marello_category_to_product')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => [
                'auditable' => true
            ]
        ]
    )]
    protected ?Collection $products = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->names = new ArrayCollection();
        $this->descriptions = new ArrayCollection();
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

    /**
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
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
     * @return LocalizedFallbackValue|null
     */
    public function getDefaultName(): ?LocalizedFallbackValue
    {
        return $this->getDefaultFallbackValue($this->names);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setDefaultName(string $name): self
    {
        $oldDefaultName = $this->getDefaultName();
        if ($oldDefaultName && $this->names->contains($oldDefaultName)) {
            $this->names->removeElement($oldDefaultName);
        }

        $newDefaultName = new LocalizedFallbackValue();
        $newDefaultName->setString($name);
        
        if (!$this->names->contains($newDefaultName)) {
            $this->names->add($newDefaultName);
        }

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
     * @param string $code
     * @return $this
     */
    public function setCode(string $code): self
    {
        $this->code = $code;

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
     * @return LocalizedFallbackValue|null
     */
    public function getDefaultDescription(): ?LocalizedFallbackValue
    {
        return $this->getDefaultFallbackValue($this->descriptions);
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDefaultDescription(?string $description): self
    {
        $oldDefaultDescription = $this->getDefaultDescription();
        if ($oldDefaultDescription && $this->descriptions->contains($oldDefaultDescription)) {
            $this->descriptions->removeElement($oldDefaultDescription);
        }

        if ($description !== null) {
            $newDefaultDescription = new LocalizedFallbackValue();
            $newDefaultDescription->setText($description);
            
            if (!$this->descriptions->contains($newDefaultDescription)) {
                $this->descriptions->add($newDefaultDescription);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function addProduct(Product $product): self
    {
        if (!$this->hasProduct($product)) {
            $this->products->add($product);
            $product->addCategoryCode($this->getCode());
        }

        return $this;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function removeProduct(Product $product): self
    {
        if ($this->hasProduct($product)) {
            $product->removeCategory($this);
            $this->products->removeElement($product);
        }

        return $this;
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
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getDenormalizedDefaultName();
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
        $defaultName = $this->getDefaultName();
        if (!$defaultName) {
            throw new \RuntimeException(sprintf('Category %s has to have a default name', $this->getCode()));
        }
        $this->denormalizedDefaultName = $defaultName->getString();
    }

    /**
     * @param Collection $values
     * @return LocalizedFallbackValue|null
     */
    protected function getDefaultFallbackValue(Collection $values): ?LocalizedFallbackValue
    {
        $filteredValues = $values->filter(
            function (LocalizedFallbackValue $value) {
                return $value->getLocalization() === null;
            }
        );

        return $filteredValues->isEmpty() ? null : $filteredValues->first();
    }
}
