<?php

namespace Marello\Bundle\InvoiceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

#[ORM\Entity]
class InvoiceItem extends AbstractInvoiceItem implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var Invoice
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "full"=true
     *          },
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     */
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[ORM\ManyToOne(targetEntity: \Invoice::class, inversedBy: 'items')]
    protected $invoice;
}
