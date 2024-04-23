<?php

namespace Marello\Bundle\ProductBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;

use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\SalesBundle\Model\SalesChannelAwareInterface;

/**
 * TaxCode
 */
#[ORM\Table(name: 'marello_prod_prod_chan_tax_rel')]
#[ORM\UniqueConstraint(name: 'marello_prod_prod_chan_tax_rel_uidx', columns: ['product_id', 'sales_channel_id', 'tax_code_id'])]
#[ORM\Entity(repositoryClass: \Marello\Bundle\ProductBundle\Entity\Repository\ProductChannelTaxRelationRepository::class)]
#[Oro\Config]
class ProductChannelTaxRelation implements SalesChannelAwareInterface
{
    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Product
     *
     *
     */
    #[ORM\JoinColumn(name: 'product_id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\ProductBundle\Entity\Product::class, inversedBy: 'salesChannelTaxCodes', cascade: ['persist'])]
    protected $product;

    /**
     * @var SalesChannel
     *
     *
     */
    #[ORM\JoinColumn(name: 'sales_channel_id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\SalesBundle\Entity\SalesChannel::class, cascade: ['persist'])]
    protected $salesChannel;

    /**
     * @var TaxCode
     *
     *
     */
    #[ORM\JoinColumn(name: 'tax_code_id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\TaxBundle\Entity\TaxCode::class, cascade: ['persist'])]
    protected $taxCode;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set product
     *
     * @param Product $product
     *
     * @return ProductChannelTaxRelation
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set salesChannel
     *
     * @param SalesChannel $salesChannel
     *
     * @return ProductChannelTaxRelation
     */
    public function setSalesChannel(SalesChannel $salesChannel)
    {
        $this->salesChannel = $salesChannel;

        return $this;
    }

    /**
     * Get salesChannel
     *
     * @return SalesChannel
     */
    public function getSalesChannel()
    {
        return $this->salesChannel;
    }

    /**
     * Set taxCode
     *
     * @param TaxCode $taxCode
     *
     * @return ProductChannelTaxRelation
     */
    public function setTaxCode(TaxCode $taxCode)
    {
        $this->taxCode = $taxCode;

        return $this;
    }

    /**
     * Get taxCode
     *
     * @return TaxCode
     */
    public function getTaxCode()
    {
        return $this->taxCode;
    }
    
    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
        }
    }
}
