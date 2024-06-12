<?php

namespace Marello\Bundle\InvoiceBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

use Marello\Bundle\PaymentTermBundle\Entity\PaymentTerm;

#[ORM\Entity]
class Invoice extends AbstractInvoice implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    const INVOICE_TYPE = 'invoice';

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     */
    protected $invoiceType = self::INVOICE_TYPE;

    /**
     * @var Collection|InvoiceItem[]
     */
    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceItem::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[Oro\ConfigField(
        defaultValues: [
            'email' => ['available_in_template' => true],
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected $items;

    /**
     * @var PaymentTerm
     */
    #[ORM\JoinColumn(name: 'payment_term_id', nullable: true, onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: PaymentTerm::class)]
    #[Oro\ConfigField(
        defaultValues: [
            'email' => ['available_in_template' => true],
            'dataaudit' => ['auditable' => true]
        ]
    )]
    protected $paymentTerm;

    /**
     * @return PaymentTerm|null
     */
    public function getPaymentTerm()
    {
        return $this->paymentTerm;
    }

    /**
     * @param PaymentTerm|null $paymentTerm
     * @return Invoice
     */
    public function setPaymentTerm(PaymentTerm $paymentTerm = null)
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }
}
