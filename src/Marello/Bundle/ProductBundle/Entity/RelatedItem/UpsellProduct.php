<?php

namespace Marello\Bundle\ProductBundle\Entity\RelatedItem;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\RelatedItem\RelatedItemEntityInterface;

/**
 * Representation of relations between upsell products
 */
#[ORM\Entity(), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_product_upsells')]
#[ORM\Index(columns: ['product_id'], name: 'idx_marello_product_upsell_product_product_id')]
#[ORM\Index(columns: ['related_item_id'], name: 'idx_marello_product_upsell_product_related_item_id')]
#[ORM\UniqueConstraint(name: 'idx_marello_product_upsell_product_unique', columns: ['product_id', 'related_item_id'])]
#[Oro\Config(
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
class UpsellProduct implements
    RelatedItemEntityInterface,
    OrganizationAwareInterface
{
    use AuditableOrganizationAwareTrait;
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected ?int $id = null;

    /**
     * @var Product
     */
    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['identity' => true]
        ]
    )]
    protected $product;

    /**
     * @var Product
     */
    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'related_item_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['identity' => true]
        ]
    )]
    protected $relatedItem;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * {@inheritDoc}
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRelatedItem()
    {
        return $this->relatedItem;
    }

    /**
     * {@inheritDoc}
     */
    public function setRelatedItem(Product $product)
    {
        $this->relatedItem = $product;

        return $this;
    }
}
