<?php

namespace Marello\Bundle\InvoiceBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\CurrencyBundle\Entity\PriceAwareInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\TaxBundle\Model\TaxAwareInterface;
use Marello\Bundle\ProductBundle\Entity\ProductInterface;
use Marello\Bundle\OrderBundle\Model\QuantityAwareInterface;
use Marello\Bundle\ProductBundle\Model\ProductAwareInterface;

#[ORM\Table(name: 'marello_invoice_invoice_item')]
#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'invoice_item_type', type: 'string')]
#[ORM\DiscriminatorMap([
    'invoiceitem' => 'Marello\Bundle\InvoiceBundle\Entity\InvoiceItem',
    'creditmemoitem' => 'Marello\Bundle\InvoiceBundle\Entity\CreditmemoItem'
])]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(
    defaultValues: [
        'dataaudit' => ['auditable' => true],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ]
    ]
)]
abstract class AbstractInvoiceItem implements
    QuantityAwareInterface,
    PriceAwareInterface,
    TaxAwareInterface,
    ProductAwareInterface,
    OrganizationAwareInterface
{
    use AuditableOrganizationAwareTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var AbstractInvoice
     */
    protected $invoice;

    /**
     * @var ProductInterface|Product
     */
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $product;

    /**
     * @var string
     */
    #[ORM\Column(name: 'product_name', type: Types::STRING, nullable: false)]
    protected $productName;

    /**
     * @var string
     */
    #[ORM\Column(name: 'product_sku', type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $productSku;

    /**
     * @var int
     */
    #[ORM\Column(name: 'price', type: 'money')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $price;

    /**
     * @var int
     */
    #[ORM\Column(name: 'quantity', type: Types::INTEGER, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $quantity;

    /**
     * @var int
     */
    #[ORM\Column(name: 'tax', type: 'money')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $tax;

    /**
     * @var double
     */
    #[ORM\Column(name: 'discount_amount', type: 'money', nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $discountAmount = 0;

    /**
     * @var int
     */
    #[ORM\Column(name: 'row_total_incl_tax', type: 'money', nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $rowTotalInclTax;

    /**
     * @var int
     */
    #[ORM\Column(name: 'row_total_excl_tax', type: 'money', nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $rowTotalExclTax;

    /**
     * @var string
     */
    #[ORM\Column(name: 'product_unit', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['excluded' => true]])]
    protected $productUnit;

    #[ORM\PrePersist]
    public function prePersist()
    {
        // prevent overriding product name if already being set
        if (is_null($this->productName)) {
            $this->setProductName((string)$this->product->getName());
        }
        $this->productSku  = $this->product->getSku();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AbstractInvoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param AbstractInvoice $invoice
     * @return AbstractInvoiceItem
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * @return Product|ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product|ProductInterface $product
     * @return AbstractInvoiceItem
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * @param string $productName
     * @return AbstractInvoiceItem
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductSku()
    {
        return $this->productSku;
    }

    /**
     * @param string $productSku
     * @return AbstractInvoiceItem
     */
    public function setProductSku($productSku)
    {
        $this->productSku = $productSku;

        return $this;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     * @return AbstractInvoiceItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return AbstractInvoiceItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * @param int $tax
     * @return AbstractInvoiceItem
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * @param float $discountAmount
     * @return AbstractInvoiceItem
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discountAmount = $discountAmount;

        return $this;
    }

    /**
     * @return int
     */
    public function getRowTotalInclTax()
    {
        return $this->rowTotalInclTax;
    }

    /**
     * @param int $rowTotalInclTax
     * @return AbstractInvoiceItem
     */
    public function setRowTotalInclTax($rowTotalInclTax)
    {
        $this->rowTotalInclTax = $rowTotalInclTax;

        return $this;
    }

    /**
     * @return int
     */
    public function getRowTotalExclTax()
    {
        return $this->rowTotalExclTax;
    }

    /**
     * @param int $rowTotalExclTax
     * @return AbstractInvoiceItem
     */
    public function setRowTotalExclTax($rowTotalExclTax)
    {
        $this->rowTotalExclTax = $rowTotalExclTax;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductUnit()
    {
        return $this->productUnit;
    }

    /**
     * @param string $productUnit
     * @return $this
     */
    public function setProductUnit($productUnit)
    {
        $this->productUnit = $productUnit;

        return $this;
    }
}
