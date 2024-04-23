<?php

namespace Marello\Bundle\InvoiceBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;

#[ORM\Entity]
class Creditmemo extends AbstractInvoice implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    const CREDITMEMO_TYPE = 'creditmemo';

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
    protected $invoiceType = self::CREDITMEMO_TYPE;

    /**
     * @var Collection|CreditmemoItem[]
     */
    #[ORM\OneToMany(targetEntity: \CreditmemoItem::class, mappedBy: 'invoice', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[Oro\ConfigField(defaultValues: ['email' => ['available_in_template' => true], 'dataaudit' => ['auditable' => true]])]
    protected $items;
}
