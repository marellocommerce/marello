<?php

namespace Marello\Bundle\PricingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
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
#[ORM\Table(name: 'marello_product_channel_price')]
#[ORM\UniqueConstraint(name: 'marello_product_channel_price_uidx', columns: ['product_id', 'channel_id', 'currency', 'type'])]
#[ORM\Entity(repositoryClass: \Marello\Bundle\PricingBundle\Entity\Repository\ProductChannelPriceRepository::class)]
class ProductChannelPrice extends BasePrice
{
    /**
     * @var Product
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\ProductBundle\Entity\Product::class, inversedBy: 'channelPrices')]
    protected $product;

    /**
     * @var SalesChannel
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(name: 'channel_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\SalesBundle\Entity\SalesChannel::class)]
    protected $channel;

    /**
     * @return SalesChannel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @param SalesChannel $channel
     *
     * @return $this
     */
    public function setChannel(SalesChannel $channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }
}
