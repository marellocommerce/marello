<?php

namespace Marello\Bundle\RefundBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\PricingBundle\Model\CurrencyAwareInterface;
use Marello\Bundle\ReturnBundle\Entity\ReturnItem;

/**
 * @Oro\Config(
 *      defaultValues={
 *          "dataaudit"={
 *              "auditable"=true
 *          },
 *          "ownership"={
 *              "owner_type"="ORGANIZATION",
 *              "owner_field_name"="organization",
 *              "owner_column_name"="organization_id"
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_refund_item')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class RefundItem implements CurrencyAwareInterface, OrganizationAwareInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;

    /**
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'name', type: 'string')]
    protected $name;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var int
     */
    #[ORM\Column(name: 'quantity', type: 'integer')]
    protected $quantity = 1;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var float
     */
    #[ORM\Column(name: 'base_amount', type: 'money')]
    protected $baseAmount = 0;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var float
     */
    #[ORM\Column(name: 'refund_amount', type: 'money')]
    protected $refundAmount = 0;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var float
     */
    #[ORM\Column(name: 'tax_total', type: 'money')]
    protected $taxTotal = 0;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var float
     */
    #[ORM\Column(name: 'subtotal', type: 'money')]
    protected $subTotal = 0;

    /**
     * @var TaxCode
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     *
     */
    #[ORM\JoinColumn(name: 'tax_code_id', referencedColumnName: 'id', onDelete: 'SET NULL', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\TaxBundle\Entity\TaxCode::class)]
    protected $taxCode;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     *
     * @var Refund
     */
    #[ORM\JoinColumn(name: 'refund_id', nullable: false, onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Refund::class, inversedBy: 'items')]
    protected $refund;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     *
     * @var OrderItem
     */
    #[ORM\JoinColumn(name: 'order_item_id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\OrderBundle\Entity\OrderItem::class)]
    protected $orderItem;

    /**
     * RefundItem constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $item
     *
     * @return RefundItem
     */
    public static function fromOrderItem(OrderItem $item)
    {
        $refund = new self();

        $discount = $item->getOrder()->getDiscountAmount() ? : 0.00;
        $baseDiscount = $discount / $item->getQuantity();
        $refund
            ->setOrderItem($item)
            ->setName($item->getProductName())
            ->setBaseAmount($item->getPurchasePriceIncl())
            ->setSubTotal($item->getOrder()->getSubtotal())
            ->setTaxTotal($item->getOrder()->getTotalTax())
            ->setBaseAmount($item->getPurchasePriceIncl() - $baseDiscount)
        ;

        return $refund;
    }

    /**
     * @param ReturnItem $item
     *
     * @return RefundItem
     */
    public static function fromReturnItem(ReturnItem $item)
    {
        $refund = new self();

        $orderItem = $item->getOrderItem();
        $discount = $orderItem->getOrder()->getDiscountAmount() ? : 0.00;
        $baseDiscount = $discount / $orderItem->getQuantity();

        $refund
            ->setOrderItem($orderItem)
            ->setName($orderItem->getProductName())
            ->setBaseAmount($orderItem->getPurchasePriceIncl() - $baseDiscount)
            ->setSubTotal($orderItem->getOrder()->getSubtotal())
            ->setTaxTotal($orderItem->getOrder()->getTotalTax())
            ->setTaxCode($orderItem->getTaxCode())
            ->setRefundAmount($refund->getBaseAmount() * $item->getQuantity())
            ->setQuantity($item->getQuantity());

        return $refund;
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return RefundItem
     */
    public function setName($name)
    {
        $this->name = $name;

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
     *
     * @return RefundItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int
     */
    public function getBaseAmount()
    {
        return $this->baseAmount;
    }

    /**
     * @param int $baseAmount
     *
     * @return RefundItem
     */
    public function setBaseAmount($baseAmount)
    {
        $this->baseAmount = $baseAmount;

        return $this;
    }

    /**
     * @return int
     */
    public function getRefundAmount()
    {
        return $this->refundAmount;
    }

    /**
     * @param int $refundAmount
     *
     * @return RefundItem
     */
    public function setRefundAmount($refundAmount)
    {
        $this->refundAmount = $refundAmount;

        return $this;
    }

    /**
     * @return float
     */
    public function getTaxTotal()
    {
        return $this->taxTotal;
    }

    /**
     * @param float $taxTotal
     */
    public function setTaxTotal($taxTotal)
    {
        $this->taxTotal = $taxTotal;

        return $this;
    }

    /**
     * @return float
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param float $subTotal
     */
    public function setSubTotal($subTotal)
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    /**
     * @return TaxCode
     */
    public function getTaxCode()
    {
        return $this->taxCode;
    }

    /**
     * @param TaxCode $taxCode
     */
    public function setTaxCode(TaxCode $taxCode = null)
    {
        $this->taxCode = $taxCode;

        return $this;
    }

    /**
     * @return Refund
     */
    public function getRefund()
    {
        return $this->refund;
    }

    /**
     * @param Refund $refund
     *
     * @return RefundItem
     */
    public function setRefund($refund)
    {
        $this->refund = $refund;

        return $this;
    }

    /**
     * @return OrderItem
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * @param OrderItem $orderItem
     *
     * @return RefundItem
     */
    public function setOrderItem($orderItem)
    {
        $this->orderItem = $orderItem;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->getRefund()->getCurrency();
    }
}
