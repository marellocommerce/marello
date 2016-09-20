<?php

namespace Marello\Component\Pricing\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Product\Model\ProductInterface;
use Marello\Component\Product\Model\ProductPriceInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

/**
 * Represents a Marello ProductPrice
 *
 * @Oro\Config(
 *  defaultValues={
 *      "entity"={"icon"="icon-usd"},
 *      "security"={
 *          "type"="ACL",
 *          "group_name"=""
 *      }
 *  }
 * )
 */
class ProductPrice extends BasePrice implements ProductPriceInterface
{
    /**
     * @var ProductInterface
     **/
    protected $product;

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param ProductInterface $product
     * @return ProductPriceInterface
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;

        return $this;
    }

    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
