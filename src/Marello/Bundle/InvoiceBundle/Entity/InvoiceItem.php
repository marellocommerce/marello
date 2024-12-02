<?php

namespace Marello\Bundle\InvoiceBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

#[ORM\Entity]
class InvoiceItem extends AbstractInvoiceItem implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Invoice
     */
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: Invoice::class, inversedBy: 'items')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['full' => true], 'dataaudit' => ['auditable' => true]])]
    protected $invoice;
}
