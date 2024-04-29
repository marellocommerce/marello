<?php

namespace Marello\Bundle\CatalogBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
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
class Category implements DatesAwareInterface, OrganizationAwareInterface, ExtendEntityInterface
{
    use DatesAwareTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true]]
    )]
    protected ?string $name = null;

    #[ORM\Column(name: 'code', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true]]
    )]
    protected ?string $code = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true]]
    )]
    protected ?string $description = null;

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
        return (string)$this->getName();
    }
}
