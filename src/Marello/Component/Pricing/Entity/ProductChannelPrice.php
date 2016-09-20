<?php

namespace Marello\Component\Pricing\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Sales\Entity\SalesChannel;
use Marello\Component\Product\Model\ProductChannelPriceInterface;
use Marello\Component\Product\Model\ProductInterface;
use Marello\Component\Sales\Model\SalesChannelInterface;
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
class ProductChannelPrice extends BasePrice implements ProductChannelPriceInterface
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var SalesChannelInterface
     */
    protected $channel;

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param ProductInterface $product
     *
     * @return $this
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return SalesChannel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param SalesChannelInterface $channel
     *
     * @return $this
     */
    public function setChannel(SalesChannelInterface $channel)
    {
        $this->channel = $channel;

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
