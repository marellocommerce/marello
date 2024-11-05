<?php

namespace Marello\Bundle\PricingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\ProductInterface;

/**
 * Represents a Marello ProductPrice
 */
#[ORM\Table(name: 'marello_product_price')]
#[ORM\UniqueConstraint(name: 'marello_product_price_uidx', columns: ['product_id', 'currency', 'type'])]
#[ORM\Entity]
#[Oro\Config(
    defaultValues: [
        'entity' => ['icon' => 'fa-usd'],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'dataaudit' => ['auditable' => true]
    ]
)]
class ProductPrice extends BasePrice
{
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'prices')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true], 'dataaudit' => ['auditable' => true]])]
    protected $product;

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param ProductInterface $product
     * @return $this
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;

        return $this;
    }
}
