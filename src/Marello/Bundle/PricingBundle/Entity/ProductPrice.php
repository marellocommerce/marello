<?php

namespace Marello\Bundle\PricingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\ProductBundle\Entity\ProductInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Marello\Bundle\ProductBundle\Entity\Product;

/**
 * Represents a Marello ProductPrice
 *
 * @Oro\Config(
 *  defaultValues={
 *      "entity"={"icon"="fa-usd"},
 *      "security"={
 *          "type"="ACL",
 *          "group_name"=""
 *      },
 *      "dataaudit"={
 *          "auditable"=true
 *      }
 *  }
 * )
 */
#[ORM\Table(name: 'marello_product_price')]
#[ORM\UniqueConstraint(name: 'marello_product_price_uidx', columns: ['product_id', 'currency', 'type'])]
#[ORM\Entity]
class ProductPrice extends BasePrice
{
    /**
     * @var Product
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "identity"=true
     *          },
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\ProductBundle\Entity\Product::class, inversedBy: 'prices')]
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
