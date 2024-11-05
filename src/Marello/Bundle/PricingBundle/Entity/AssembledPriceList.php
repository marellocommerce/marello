<?php

namespace Marello\Bundle\PricingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\PricingBundle\Model\PriceTypeInterface;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;

#[ORM\Table(name: 'marello_assembled_price_list')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(defaultValues: ['dataaudit' => ['auditable' => true]])]
class AssembledPriceList implements PriceListInterface, ExtendEntityInterface
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
    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'prices')]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['order' => 5, 'full' => false, 'identity' => true],
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected $product;

    /**
     * @var ProductPrice
     */
    #[ORM\JoinColumn(name: 'default_price_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\OneToOne(targetEntity: ProductPrice::class, cascade: ['persist'], orphanRemoval: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['order' => 15, 'full' => true],
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected $defaultPrice;

    /**
     * @var ProductPrice
     */
    #[ORM\JoinColumn(name: 'special_price_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    #[ORM\OneToOne(targetEntity: ProductPrice::class, cascade: ['persist'], orphanRemoval: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['order' => 20, 'full' => true],
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected $specialPrice;

    /**
     * @var ProductPrice
     */
    #[ORM\JoinColumn(name: 'msrp_price_id', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    #[ORM\OneToOne(targetEntity: ProductPrice::class, cascade: ['persist'], orphanRemoval: true)]
    #[Oro\ConfigField(
        defaultValues: [
            'importexport' => ['order' => 25, 'full' => true],
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected $msrpPrice;

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
        if ($this->msrpPrice) {
            $this->msrpPrice->setProduct($product);
        }

        return $this;
    }

    /**
     * Get a Price from the price list
     * By default, get the default price
     * @param string $type
     * @return ProductPrice
     */
    public function getPrice($type = PriceTypeInterface::DEFAULT_PRICE)
    {
        if ($type === PriceTypeInterface::SPECIAL_PRICE) {
            return $this->getSpecialPrice();
        }

        if ($type === PriceTypeInterface::MSRP_PRICE) {
            return $this->getMsrpPrice();
        }

        return $this->getDefaultPrice();
    }

    /**
     * @return ProductPrice
     */
    public function getDefaultPrice()
    {
        return $this->defaultPrice;
    }

    /**
     * @param ProductPrice $defaultPrice
     * @return $this
     */
    public function setDefaultPrice(ProductPrice $defaultPrice)
    {
        if ($this->product) {
            $defaultPrice->setProduct($this->product);
        }
        if ($this->currency) {
            $defaultPrice->setCurrency($this->currency);
        }
        $this->defaultPrice = $defaultPrice;

        return $this;
    }

    /**
     * @return ProductPrice
     */
    public function getSpecialPrice()
    {
        return $this->specialPrice;
    }

    /**
     * @param ProductPrice $specialPrice
     * @return $this
     */
    public function setSpecialPrice(ProductPrice $specialPrice = null)
    {
        if ($specialPrice) {
            if ($this->product) {
                $specialPrice->setProduct($this->product);
            }
            if ($this->currency) {
                $specialPrice->setCurrency($this->currency);
            }
        }
        $this->specialPrice = $specialPrice;

        return $this;
    }

    /**
     * @return ProductPrice
     */
    public function getMsrpPrice()
    {
        return $this->msrpPrice;
    }

    /**
     * @param ProductPrice $msrpPrice
     * @return $this
     */
    public function setMsrpPrice(ProductPrice $msrpPrice = null)
    {
        if ($msrpPrice) {
            if ($this->product) {
                $msrpPrice->setProduct($this->product);
            }
            if ($this->currency) {
                $msrpPrice->setCurrency($this->currency);
            }
        }
        $this->msrpPrice = $msrpPrice;

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
        if ($this->msrpPrice) {
            $this->msrpPrice = clone $this->getMsrpPrice();
        }
    }
}
