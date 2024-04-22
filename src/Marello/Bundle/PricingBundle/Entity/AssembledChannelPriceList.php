<?php

namespace Marello\Bundle\PricingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\PricingBundle\Model\PriceTypeInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

/**
 * @Oro\Config(
 *      defaultValues={
 *          "dataaudit"={
 *              "auditable"=true
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_assembled_ch_pr_list')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class AssembledChannelPriceList implements PriceListInterface, ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use ExtendEntityTrait;
    
    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    protected $id;

    /**
     * @var string
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "identity"=true,
     *          },
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'currency', type: 'string', length: 3)]
    protected $currency;

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
     * @var ProductChannelPrice
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
    #[ORM\JoinColumn(name: 'default_price_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\OneToOne(targetEntity: \Marello\Bundle\PricingBundle\Entity\ProductChannelPrice::class, cascade: ['persist'], orphanRemoval: true)]
    protected $defaultPrice;

    /**
     * @var ProductChannelPrice
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
    #[ORM\JoinColumn(name: 'special_price_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    #[ORM\OneToOne(targetEntity: \Marello\Bundle\PricingBundle\Entity\ProductChannelPrice::class, cascade: ['persist'], orphanRemoval: true)]
    protected $specialPrice;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

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
        if ($this->defaultPrice) {
            $this->defaultPrice->setProduct($product);
        }
        if ($this->specialPrice) {
            $this->specialPrice->setProduct($product);
        }

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
     * @param SalesChannel $channel
     * @return $this
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;
        if ($this->defaultPrice) {
            $this->defaultPrice->setChannel($channel);
        }
        if ($this->specialPrice) {
            $this->specialPrice->setChannel($channel);
        }

        return $this;
    }

    /**
     * @return ProductChannelPrice
     */
    public function getDefaultPrice()
    {
        return $this->defaultPrice;
    }

    /**
     * Get a Price from the price list
     * By default, get the default price
     * @param string $type
     * @return ProductChannelPrice
     */
    public function getPrice($type = PriceTypeInterface::DEFAULT_PRICE)
    {
        if ($type === PriceTypeInterface::SPECIAL_PRICE) {
            return $this->getSpecialPrice();
        }

        return $this->getDefaultPrice();
    }

    /**
     * @param ProductChannelPrice $defaultPrice
     * @return $this
     */
    public function setDefaultPrice(ProductChannelPrice $defaultPrice)
    {
        if ($this->product) {
            $defaultPrice->setProduct($this->product);
        }
        if ($this->channel) {
            $defaultPrice->setChannel($this->channel);
        }
        if ($this->currency) {
            $defaultPrice->setCurrency($this->currency);
        }
        $this->defaultPrice = $defaultPrice;

        return $this;
    }

    /**
     * @return ProductChannelPrice
     */
    public function getSpecialPrice()
    {
        return $this->specialPrice;
    }

    /**
     * @param ProductChannelPrice $specialPrice
     * @return $this
     */
    public function setSpecialPrice(ProductChannelPrice $specialPrice = null)
    {
        if ($specialPrice) {
            if ($this->product) {
                $specialPrice->setProduct($this->product);
            }
            if ($this->channel) {
                $specialPrice->setChannel($this->channel);
            }
            if ($this->currency) {
                $specialPrice->setCurrency($this->currency);
            }
        }
        $this->specialPrice = $specialPrice;

        return $this;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
        }
        if ($this->defaultPrice) {
            $this->defaultPrice = clone $this->getDefaultPrice();
        }
        if ($this->specialPrice) {
            $this->specialPrice = clone $this->getSpecialPrice();
        }
    }
}
