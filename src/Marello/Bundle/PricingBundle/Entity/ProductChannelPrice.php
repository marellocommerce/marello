<?php

namespace Marello\Bundle\PricingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\PricingBundle\Entity\Repository\ProductChannelPriceRepository;

/**
 * Represents a Marello ProductChannelPrice
 */
#[ORM\Table(name: 'marello_product_channel_price')]
#[ORM\UniqueConstraint(
    name: 'marello_product_channel_price_uidx',
    columns: ['product_id', 'channel_id', 'currency', 'type']
)]
#[ORM\Entity(repositoryClass: ProductChannelPriceRepository::class)]
#[Oro\Config(
    defaultValues: [
        'entity' => ['icon' => 'fa-usd'],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'dataaudit' => ['auditable' => true]
    ]
)]
class ProductChannelPrice extends BasePrice
{
    /**
     * @var Product
     */
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'channelPrices')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true], 'dataaudit' => ['auditable' => true]])]
    protected $product;

    /**
     * @var SalesChannel
     */
    #[ORM\JoinColumn(name: 'channel_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: SalesChannel::class)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true], 'dataaudit' => ['auditable' => true]])]
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
