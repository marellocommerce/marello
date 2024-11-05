<?php

namespace Marello\Bundle\PaymentTermBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;

use Marello\Bundle\PaymentTermBundle\Form\Type\PaymentTermSelectType;

#[ORM\Entity, ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_payment_term')]
#[ORM\UniqueConstraint(columns: ['code'])]
#[Oro\Config(
    routeName: 'marello_paymentterm_paymentterm_index',
    routeView: 'marello_paymentterm_paymentterm_view',
    routeUpdate: 'marello_paymentterm_paymentterm_update',
    defaultValues: [
        'entity' => [
            'icon' => 'fa-usd',
        ],
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'form' => ['form_type' => PaymentTermSelectType::class, 'grid_name' => 'marello-payment-terms-select-grid']
    ]
)]
class PaymentTerm implements ExtendEntityInterface
{
    use ExtendEntityTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['excluded' => true]])]
    protected ?int $id = null;

    #[ORM\Column(name: 'code', type: Types::STRING, length: 32, nullable: false)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['identity' => true, 'order' => 10]]
    )]
    protected ?string $code = null;

    #[ORM\Column(name: 'term', type: Types::INTEGER)]
    #[Oro\ConfigField(
        defaultValues: ['dataaudit' => ['auditable' => true], 'importexport' => ['identity' => true, 'order' => 20]]
    )]
    protected ?int $term = null;

    #[ORM\ManyToMany(targetEntity: LocalizedFallbackValue::class, cascade: ['ALL'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'marello_payment_term_labels')]
    #[ORM\JoinColumn(name: 'paymentterm_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'localized_value_id', referencedColumnName: 'id', unique: true, onDelete: 'CASCADE')]
    #[Oro\ConfigField(
        defaultValues: [
            'dataaudit' => ['auditable' => true],
            'importexport' => ['order' => 30, 'full' => true, 'fallback_field' => 'string']
        ]
    )]
    protected ?Collection $labels = null;

    public function __construct()
    {
        $this->labels = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code = null) : self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return int
     */
    public function getTerm(): ?int
    {
        return $this->term;
    }

    /**
     * @param int $term
     * @return $this
     */
    public function setTerm(int $term): self
    {
        $this->term = $term;

        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getLabels(): ?Collection
    {
        return $this->labels;
    }

    /**
     * @param $label LocalizedFallbackValue
     * @return $this
     */
    public function addLabel(LocalizedFallbackValue $label): self
    {
        if (!$this->labels->contains($label)) {
            $this->labels[] = $label;
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $label
     * @return $this
     */
    public function removeLabel(LocalizedFallbackValue $label): self
    {
        $this->labels->removeElement($label);

        return $this;
    }
}
