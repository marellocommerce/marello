<?php

namespace Marello\Bundle\BankTransferBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\ParameterBag;

use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;

use Marello\Bundle\BankTransferBundle\Entity\Repository\BankTransferSettingsRepository;

#[ORM\Entity(BankTransferSettingsRepository::class)]
class BankTransferSettings extends Transport
{
    #[ORM\ManyToMany(targetEntity: LocalizedFallbackValue::class, cascade: ['ALL'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'marello_bank_transfer_tr_lbl')]
    #[ORM\JoinColumn(name: 'transport_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'localized_value_id', referencedColumnName: 'id', unique: true, onDelete: 'CASCADE')]
    private ?Collection $labels = null;

    /**
     * @var ParameterBag
     */
    private $settings;

    public function __construct()
    {
        $this->labels = new ArrayCollection();
    }

    /**
     * @return Collection|LocalizedFallbackValue[]
     */
    public function getLabels(): ?Collection
    {
        return $this->labels;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return $this
     */
    public function addLabel(LocalizedFallbackValue $label): self
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return $this
     */
    public function removeLabel(LocalizedFallbackValue $label): self
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
        }

        return $this;
    }

    /**
     * @return ParameterBag
     */
    public function getSettingsBag(): ParameterBag
    {
        if (null === $this->settings) {
            $this->settings = new ParameterBag(
                [
                    'labels' => $this->getLabels()->toArray(),
                ]
            );
        }

        return $this->settings;
    }
}
