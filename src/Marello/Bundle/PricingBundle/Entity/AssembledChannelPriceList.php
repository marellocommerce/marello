<?php

namespace Marello\Bundle\PricingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\PricingBundle\Model\PriceTypeInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;

#[ORM\Table(name: 'marello_assembled_ch_pr_list')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(defaultValues: ['dataaudit' => ['auditable' => true]])]
class AssembledChannelPriceList implements PriceListInterface, ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use ExtendEntityTrait;

    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'currency', type: Types::STRING, length: 3)]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['order' => 10, 'identity' => true],
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected $currency;

    /**
     * @var Product
     */
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'channelPrices')]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['order' => 5, 'identity' => true, 'full' => false],
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected $product;

    /**
     * @var SalesChannel
     */
    #[ORM\JoinColumn(name: 'channel_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: SalesChannel::class)]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['identity' => true, 'full' => false],
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected $channel;

    /**
     * @var ProductChannelPrice
     */
    #[ORM\JoinColumn(name: 'default_price_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\OneToOne(targetEntity: ProductChannelPrice::class, cascade: ['persist'], orphanRemoval: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['order' => 10, 'full' => true],
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected $defaultPrice;

    /**
     * @var ProductChannelPrice
     */
    #[ORM\JoinColumn(name: 'special_price_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    #[ORM\OneToOne(targetEntity: ProductChannelPrice::class, cascade: ['persist'], orphanRemoval: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['order' => 20, 'full' => true],
            'dataaudit' => ['auditable' => true]
        ]
    )]
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
