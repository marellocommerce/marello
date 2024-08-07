<?php

namespace Marello\Bundle\ProductBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareTrait;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityBundle\EntityProperty\DatesAwareInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeFamily;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\EntityBundle\EntityProperty\DenormalizedPropertyAwareInterface;
use Oro\Bundle\EntityConfigBundle\Attribute\Entity\AttributeFamilyAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;
use Marello\Bundle\PricingBundle\Entity\PriceListInterface;
use Marello\Bundle\PricingBundle\Model\PricingAwareInterface;
use Marello\Bundle\SalesBundle\Model\SalesChannelsAwareInterface;
use Marello\Bundle\PricingBundle\Entity\AssembledChannelPriceList;
use Marello\Bundle\ProductBundle\Entity\Repository\ProductRepository;

#[ORM\Entity(ProductRepository::class), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_product_product')]
#[ORM\Index(columns: ['created_at'], name: 'idx_marello_product_created_at')]
#[ORM\Index(columns: ['updated_at'], name: 'idx_marello_product_updated_at')]
#[ORM\UniqueConstraint(name: 'marello_product_product_skuorgidx', columns: ['sku', 'organization_id'])]
#[Oro\Config(
    routeName: 'marello_product_index',
    routeView: 'marello_product_view',
    routeCreate: 'marello_product_create',
    routeUpdate: 'marello_product_update',
    defaultValues: [
        'entity' => [
            'icon' => 'fa-barcode',
        ],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'attribute' => ['has_attributes' => true],
        'tag' => ['enabled' => true]
    ]
)]
class Product implements
    ProductInterface,
    SalesChannelsAwareInterface,
    PricingAwareInterface,
    OrganizationAwareInterface,
    AttributeFamilyAwareInterface,
    DenormalizedPropertyAwareInterface,
    DatesAwareInterface,
    ExtendEntityInterface
{
    use DatesAwareTrait, ExtendEntityTrait, AuditableOrganizationAwareTrait;

    const DEFAULT_PRODUCT_TYPE = 'simple';
 
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected ?int $id = null;

    /**
     * This is a mirror field for performance reasons only.
     * It mirrors getDefaultName()->getString().
    */
    #[ORM\Column(name: 'name', type: Types::STRING, length:255, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' => true]
        ],
        mode: 'hidden'
    )]
    protected ?string $denormalizedDefaultName = null;

    #[ORM\ManyToMany(targetEntity: LocalizedFallbackValue::class, cascade: ['ALL'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'marello_product_product_name')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
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

    #[ORM\Column(name: 'sku', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['order' => 10, 'identity' => true, 'header' => 'SKU'],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?string $sku = null;

    #[ORM\Column(name: 'barcode', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'Custom']
        ]
    )]
    protected ?string $barcode = null;

    #[ORM\Column(name: 'manufacturing_code', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'Custom']
        ]
    )]
    protected ?string $manufacturingCode = null;

    #[ORM\ManyToOne(targetEntity: ProductStatus::class)]
    #[ORM\JoinColumn(name: 'product_status', referencedColumnName: 'name', onDelete: 'SET NULL')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['order' => 30, 'full' => false],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?ProductStatus $status = null;

    #[ORM\Column(name: 'type', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true]
        ]
    )]
    protected ?string $type = self::DEFAULT_PRODUCT_TYPE;

    #[ORM\Column(name: 'weight', type: Types::FLOAT, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'Custom']
        ]
    )]
    protected ?float $weight = 0;

    #[ORM\Column(name: 'warranty', type: Types::INTEGER, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'Custom']
        ]
    )]
    protected ?int $warranty = null;

    #[ORM\OneToMany(
        mappedBy: 'product',
        targetEntity: AssembledPriceList::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?Collection $prices = null;

    #[ORM\OneToMany(
        mappedBy: 'product',
        targetEntity: AssembledChannelPriceList::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?Collection $channelPrices = null;

    #[ORM\ManyToMany(targetEntity: SalesChannel::class, inversedBy: 'products', fetch: 'EAGER')]
    #[ORM\JoinTable(name: 'marello_product_saleschannel')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['order' => 40, 'full' => false],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?Collection $channels = null;

    #[ORM\Column(name: 'channels_codes', type: Types::TEXT, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true]
        ]
    )]
    protected ?string $channelsCodes = null;

    #[ORM\ManyToOne(targetEntity: Variant::class, inversedBy: 'products')]
    #[ORM\JoinColumn(name: 'variant_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true]
        ]
    )]
    protected ?Variant $variant = null;

    #[ORM\OneToOne(
        mappedBy: 'product',
        targetEntity: InventoryItem::class,
        cascade: ['remove', 'persist'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true]
        ]
    )]
    protected ?InventoryItem $inventoryItem = null;

    #[ORM\Column(name: 'data', type: Types::JSON, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['excluded' =>true]
        ]
    )]
    protected ?array $data = [];

    #[ORM\OneToMany(
        mappedBy: 'product',
        targetEntity: ProductSupplierRelation::class,
        cascade: ['remove', 'persist'],
        orphanRemoval: true
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'Custom']
        ]
    )]
    protected ?Collection $suppliers = null;

    #[ORM\ManyToOne(targetEntity: Supplier::class)]
    #[ORM\JoinColumn(name: 'preferred_supplier_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true]
        ]
    )]
    protected ?Supplier $preferredSupplier = null;

    #[ORM\ManyToOne(targetEntity: TaxCode::class)]
    #[ORM\JoinColumn(name: 'tax_code_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['order' => 40, 'full' => false],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?TaxCode $taxCode = null;

    #[ORM\OneToMany(
        mappedBy: 'product',
        targetEntity: ProductChannelTaxRelation::class,
        cascade: ['remove', 'persist'],
        orphanRemoval: true
    )]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'System']
        ]
    )]
    protected ?Collection $salesChannelTaxCodes = null;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'products', cascade: ['persist'], fetch: 'EAGER')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['order' => 50, 'full' => false],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'Custom']
        ]
    )]
    protected ?Collection $categories = null;

    #[ORM\Column(name: 'categories_codes', type: Types::TEXT, nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['excluded' =>true]
        ]
    )]
    protected ?string $categoriesCodes = null;

    #[ORM\ManyToOne(targetEntity: AttributeFamily::class)]
    #[ORM\JoinColumn(name: 'attribute_family_id', referencedColumnName: 'id', onDelete: 'RESTRICT')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => false],
            'importexport' => ['order' => 35, 'full' => false]
        ]
    )]
    protected ?AttributeFamily $attributeFamily = null;

    #[ORM\OneToOne(targetEntity: File::class, cascade: 'ALL')]
    #[ORM\JoinColumn(name: 'ar_file_id', referencedColumnName: 'id', nullable: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => false],
            'importexport' => ['excluded' => true],
            'attribute' => ['is_attribute' => true],
            'extend' => ['owner' => 'Custom'],
            'attachment' => ['maxsize' => 50, 'mimetypes' => 'application/zip,model/vnd.usdz+zip']
        ]
    )]
    protected $ARFile;

    public function __construct()
    {
        $this->names                = new ArrayCollection();
        $this->prices               = new ArrayCollection();
        $this->channelPrices        = new ArrayCollection();
        $this->channels             = new ArrayCollection();
        $this->suppliers            = new ArrayCollection();
        $this->salesChannelTaxCodes = new ArrayCollection();
        $this->categories           = new ArrayCollection();
    }
    
    public function __clone()
    {
        if ($this->id) {
            $this->id                   = null;
            $this->names                = new ArrayCollection();
            $this->prices               = new ArrayCollection();
            $this->channelPrices        = new ArrayCollection();
            $this->channels             = new ArrayCollection();
            $this->suppliers            = new ArrayCollection();
            $this->salesChannelTaxCodes = new ArrayCollection();
        }
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->setCreatedAt($now);
        $this->setUpdatedAt($now);

        if (!$this->getDefaultName()) {
            throw new \RuntimeException(sprintf('Product %s has to have a default name', $this->getSku()));
        }
        $this->updateDenormalizedProperties();
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));

        if (!$this->getDefaultName()) {
            throw new \RuntimeException(sprintf('Product %s has to have a default name', $this->getSku()));
        }
        $this->updateDenormalizedProperties();
    }

    /**
     * @return int
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
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     *
     * @return Product
     */
    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    /**
     * @param string|null $barcode
     * @return $this
     */
    public function setBarcode(string $barcode = null): self
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * @return string
     */
    public function getManufacturingCode(): ?string
    {
        return $this->manufacturingCode;
    }

    /**
     * @param string $manufacturingCode
     */
    public function setManufacturingCode(string $manufacturingCode = null): self
    {
        $this->manufacturingCode = $manufacturingCode;

        return $this;
    }

    /**
     * @return ProductStatus
     */
    public function getStatus(): ?ProductStatus
    {
        return $this->status;
    }

    /**
     * @param ProductStatus $status
     *
     * @return Product
     */
    public function setStatus(ProductStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $currency
     * @return AssembledPriceList
     */
    public function getPrice(string $currency = null): AssembledPriceList
    {
        if ($currency) {
            /** @var $productPrice */
            $productPrice = $this->getPrices()
                ->filter(function ($productPrice) use ($currency) {
                    /** @var AssembledPriceList $productPrice */
                    return $productPrice->getCurrency() === $currency;
                })
                ->first();

            if ($productPrice) {
                return $productPrice;
            }
        }
        
        return $this->prices->first();
    }

    /**
     * @return ArrayCollection|AssembledPriceList[]
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    /**
     * Add item
     *
     * @param AssembledPriceList $price
     *
     * @return Product
     */
    public function addPrice(AssembledPriceList $price): self
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->setProduct($this);
        }

        return $this;
    }

    /**
     * Remove item
     *
     * @param AssembledPriceList $price
     *
     * @return Product
     */
    public function removePrice(AssembledPriceList $price): self
    {
        if ($this->prices->contains($price)) {
            $this->prices->removeElement($price);
        }

        return $this;
    }

    /**
     * has prices
     * @return bool
     */
    public function hasPrices(): bool
    {
        return count($this->prices) > 0;
    }

    /**
     * @return ArrayCollection|AssembledChannelPriceList[]
     */
    public function getChannelPrices(): Collection
    {
        return $this->channelPrices;
    }

    /**
     * Add item
     *
     * @param AssembledChannelPriceList $channelPrice
     *
     * @return Product
     */
    public function addChannelPrice(AssembledChannelPriceList $channelPrice): self
    {
        if (!$this->channelPrices->contains($channelPrice)) {
            $this->channelPrices->add($channelPrice);
            $channelPrice->setProduct($this);
        }

        return $this;
    }

    /**
     * Remove item
     *
     * @param AssembledChannelPriceList $channelPrice
     *
     * @return Product
     */
    public function removeChannelPrice(AssembledChannelPriceList $channelPrice): self
    {
        if ($this->channelPrices->contains($channelPrice)) {
            $this->channelPrices->removeElement($channelPrice);
        }

        return $this;
    }

    /**
     * has channel prices
     * @return bool
     */
    public function hasChannelPrices(): bool
    {
        return count($this->channelPrices) > 0;
    }

    /**
     * @return ArrayCollection|SalesChannel[]
     */
    public function getChannels(): Collection
    {
        return $this->channels;
    }

    /**
     * @return Variant
     */
    public function getVariant(): ?Variant
    {
        return $this->variant;
    }

    /**
     * @param Variant $variant
     *
     * @return Product
     */
    public function setVariant(Variant $variant = null): self
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * Add item
     *
     * @param SalesChannel $channel
     *
     * @return Product
     */
    public function addChannel(SalesChannel $channel): self
    {
        if (!$this->channels->contains($channel)) {
            $this->channels->add($channel);
            $channel->addProduct($this);
            $this->addChannelCode($channel->getCode());
        }

        return $this;
    }
    
    /**
     * @param string $code
     * @return $this
     */
    public function addChannelCode($code): self
    {
        if (strpos($this->channelsCodes, '|') === false) {
            $channelsCodes = [];
        } else {
            $channelsCodes = $this->channelsCodes;
            if (substr($channelsCodes, 0, 1) === '|') {
                $channelsCodes = substr($channelsCodes, 1);
            }
            if (substr($channelsCodes, -1, 1) === '|') {
                $channelsCodes = substr($channelsCodes, 0, -1);
            }
            $channelsCodes = explode("|", $channelsCodes);
        }
        if (!in_array($code, $channelsCodes)) {
            $channelsCodes[] = $code;
            $this->channelsCodes = sprintf('|%s|', implode('|', $channelsCodes));
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasChannels(): bool
    {
        return count($this->channels) > 0;
    }
    
    /**
     * @param SalesChannel $channel
     * @return bool
     */
    public function hasChannel(SalesChannel $channel): bool
    {
        return $this->channels->contains($channel);
    }

    /**
     * Remove item
     *
     * @param SalesChannel $channel
     *
     * @return Product
     */
    public function removeChannel(SalesChannel $channel): self
    {
        if ($this->channels->contains($channel)) {
            $this->channels->removeElement($channel);
            $channelsCodes = $this->channelsCodes;
            if (substr($channelsCodes, 0, 1) === '|') {
                $channelsCodes = substr($channelsCodes, 1);
            }
            if (substr($channelsCodes, -1, 1) === '|') {
                $channelsCodes = substr($channelsCodes, 0, -1);
            }
            $channelsCodes = explode("|", $channelsCodes);
            $channelsCodes = array_diff($channelsCodes, [$channel->getCode()]);
            $this->channelsCodes = sprintf('|%s|', implode('|', $channelsCodes));
        }

        return $this;
    }

    public function clearChannels(): self
    {
        /** @var SalesChannel $channel */
        foreach ($this->channels as $channel) {
            $channel->removeProduct($this);
        }

        $this->channels->clear();
        $this->channelsCodes = '';

        return $this;
    }

    /**
     * @param array $data
     * @return Product
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
    
    /**
     * @return string
     */
    public function __toString(): string
    {
        try {
            if ($this->getDefaultName()) {
                return (string) $this->getDefaultName();
            } else {
                return (string) $this->sku;
            }
        } catch (\LogicException $e) {
            return (string) $this->sku;
        }
    }

    /**
     * @return InventoryItem|null
     */
    public function getInventoryItem(): ?InventoryItem
    {
        return $this->inventoryItem;
    }

    /**
     * @param InventoryItem $item
     *
     * @return $this
     */
    public function setInventoryItem(InventoryItem $item): self
    {
        $this->inventoryItem = $item;

        return $this;
    }

    /**
     * @return float
     */
    public function getWeight(): ?float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     *
     * @return Product
     */
    public function setWeight(float $weight = null): self
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return integer
     */
    public function getWarranty(): ?int
    {
        return $this->warranty;
    }

    /**
     * @param integer $warranty
     *
     * @return Product
     */
    public function setWarranty(int $warranty = null): self
    {
        $this->warranty = $warranty;

        return $this;
    }

    /**
     * @return ArrayCollection|ProductSupplierRelation[]
     */
    public function getSuppliers(): Collection
    {
        return $this->suppliers;
    }

    /**
     * Add item
     *
     * @param ProductSupplierRelation $supplier
     *
     * @return Product
     */
    public function addSupplier(ProductSupplierRelation $supplier): self
    {
        if (!$this->suppliers->contains($supplier)) {
            $this->suppliers->add($supplier);
            $supplier->setProduct($this);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSuppliers(): bool
    {
        return count($this->suppliers) > 0;
    }

    /**
     * Remove item
     *
     * @param ProductSupplierRelation $supplier
     *
     * @return Product
     */
    public function removeSupplier(ProductSupplierRelation $supplier): self
    {
        if ($this->suppliers->contains($supplier)) {
            $this->suppliers->removeElement($supplier);
        }

        return $this;
    }
    
    /**
     * @return Supplier
     */
    public function getPreferredSupplier(): ?Supplier
    {
        return $this->preferredSupplier;
    }

    /**
     * @param Supplier $preferredSupplier
     *
     * @return Product
     */
    public function setPreferredSupplier(Supplier $preferredSupplier = null): self
    {
        $this->preferredSupplier = $preferredSupplier;

        return $this;
    }

    /**
     * Set taxCode
     *
     * @param TaxCode $taxCode
     *
     * @return Product
     */
    public function setTaxCode(TaxCode $taxCode = null): self
    {
        $this->taxCode = $taxCode;

        return $this;
    }

    /**
     * Get taxCode
     *
     * @return TaxCode
     */
    public function getTaxCode(): ?TaxCode
    {
        return $this->taxCode;
    }

    /**
     * Add salesChannelTaxCode
     *
     * @param ProductChannelTaxRelation $salesChannelTaxCode
     *
     * @return Product
     */
    public function addSalesChannelTaxCode(ProductChannelTaxRelation $salesChannelTaxCode): self
    {
        if (!$this->salesChannelTaxCodes->contains($salesChannelTaxCode)) {
            $this->salesChannelTaxCodes->add($salesChannelTaxCode);
            $salesChannelTaxCode->setProduct($this);
        }

        return $this;
    }

    /**
     * Remove salesChannelTaxCode
     *
     * @param ProductChannelTaxRelation $salesChannelTaxCode
     *
     * @return Product
     */
    public function removeSalesChannelTaxCode(ProductChannelTaxRelation $salesChannelTaxCode): self
    {
        if ($this->salesChannelTaxCodes->contains($salesChannelTaxCode)) {
            $this->salesChannelTaxCodes->removeElement($salesChannelTaxCode);
        }

        return $this;
    }

    /**
     * Get salesChannelTaxCodes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSalesChannelTaxCodes(): Collection
    {
        return $this->salesChannelTaxCodes;
    }

    /**
     * Get salesChannelTaxCode
     *
     * @param SalesChannel $salesChannel
     * @return TaxCode|null
     */
    public function getSalesChannelTaxCode(SalesChannel $salesChannel): ?TaxCode
    {
        /** @var ProductChannelTaxRelation $productChannelTaxRelation */
        $productChannelTaxRelation = $this->getSalesChannelTaxCodes()
            ->filter(function ($productChannelTaxRelation) use ($salesChannel) {
                /** @var ProductChannelTaxRelation $productChannelTaxRelation */
                return $productChannelTaxRelation->getSalesChannel() === $salesChannel;
            })
            ->first();

        if ($productChannelTaxRelation) {
            return $productChannelTaxRelation->getTaxCode();
        }

        return null;
    }

    /**
     * @param SalesChannel $salesChannel
     * @return PriceListInterface|null
     */
    public function getSalesChannelPrice(SalesChannel $salesChannel): ?PriceListInterface
    {
        /** @var AssembledChannelPriceList $productChannelPrice */
        $productChannelPrice = $this->getChannelPrices()
            ->filter(function ($productChannelPrice) use ($salesChannel) {
                /** @var AssembledChannelPriceList $productChannelPrice */
                return $productChannelPrice->getChannel() === $salesChannel;
            })
            ->first();

        if ($productChannelPrice) {
            return $productChannelPrice;
        }

        return $this->getPrice($salesChannel->getCurrency());
    }
    
    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function addCategory(Category $category): self
    {
        if (!$this->hasCategory($category)) {
            $this->categories->add($category);
            $category->addProduct($this);
            $this->addCategoryCode($category->getCode());
        }

        return $this;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function addCategoryCode(string $code): self
    {
        if (strpos($this->categoriesCodes, '|') === false) {
            $categoriesCodes = [];
        } else {
            $categoriesCodes = $this->categoriesCodes;
            if (substr($categoriesCodes, 0, 1) === '|') {
                $categoriesCodes = substr($categoriesCodes, 1);
            }
            if (substr($categoriesCodes, -1, 1) === '|') {
                $categoriesCodes = substr($categoriesCodes, 0, -1);
            }
            $categoriesCodes = explode("|", $categoriesCodes);
        }
        if (!in_array($code, $categoriesCodes)) {
            $categoriesCodes[] = $code;
            $this->categoriesCodes = sprintf('|%s|', implode('|', $categoriesCodes));
        }

        return $this;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function removeCategory(Category $category): self
    {
        if ($this->hasCategory($category)) {
            $this->categories->removeElement($category);
            $category->removeProduct($this);
            $categoriesCodes = $this->categoriesCodes;
            if (substr($categoriesCodes, 0, 1) === '|') {
                $categoriesCodes = substr($categoriesCodes, 1);
            }
            if (substr($categoriesCodes, -1, 1) === '|') {
                $categoriesCodes = substr($categoriesCodes, 0, -1);
            }
            $categoriesCodes = explode("|", $categoriesCodes);
            $categoriesCodes = array_diff($categoriesCodes, [$category->getCode()]);
            $this->categoriesCodes = sprintf('|%s|', implode('|', $categoriesCodes));
        }

        return $this;
    }
    

    /**
     * @param Category $category
     * @return bool
     */
    public function hasCategory(Category $category): bool
    {
        return $this->categories->contains($category);
    }

    public function clearCategories(): self
    {
        $this->categories->clear();
        $this->categoriesCodes = '';

        return $this;
    }

    /**
     * @return AttributeFamily
     */
    public function getAttributeFamily(): ?AttributeFamily
    {
        return $this->attributeFamily;
    }

    /**
     * @param AttributeFamily $attributeFamily
     * @return $this
     */
    public function setAttributeFamily(AttributeFamily $attributeFamily): self
    {
        $this->attributeFamily = $attributeFamily;

        return $this;
    }

    public function getARFile(): ?File
    {
        return $this->ARFile;
    }

    public function setARFile(File $ARFile): self
    {
        $this->ARFile = $ARFile;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Product
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        
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
            throw new \RuntimeException(sprintf('Product %s has to have a default name', $this->getSku()));
        }
        $this->denormalizedDefaultName = $this->getDefaultName()->getString();
    }
}
