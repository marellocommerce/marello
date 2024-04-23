<?php

namespace Marello\Bundle\PaymentTermBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\IntegrationBundle\Entity\Transport;
use Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue;
use Symfony\Component\HttpFoundation\ParameterBag;

#[ORM\Entity(repositoryClass: \Marello\Bundle\PaymentTermBundle\Entity\Repository\MarelloPaymentTermSettingsRepository::class)]
class MarelloPaymentTermSettings extends Transport
{
    const SETTINGS_FIELD_LABELS = 'labels';

    /**
     * @var Collection|LocalizedFallbackValue[]
     */
    #[ORM\JoinTable(name: 'marello_payment_term_trans_lbl')]
    #[ORM\JoinColumn(name: 'transport_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'localized_value_id', referencedColumnName: 'id', onDelete: 'CASCADE', unique: true)]
    #[ORM\ManyToMany(targetEntity: \Oro\Bundle\LocaleBundle\Entity\LocalizedFallbackValue::class, cascade: ['ALL'], orphanRemoval: true)]
    private $labels;

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
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return MarelloPaymentTermSettings
     */
    public function addLabel(LocalizedFallbackValue $label)
    {
        if (!$this->labels->contains($label)) {
            $this->labels->add($label);
        }

        return $this;
    }

    /**
     * @param LocalizedFallbackValue $label
     *
     * @return MarelloPaymentTermSettings
     */
    public function removeLabel(LocalizedFallbackValue $label)
    {
        if ($this->labels->contains($label)) {
            $this->labels->removeElement($label);
        }

        return $this;
    }

    /**
     * @return ParameterBag
     */
    public function getSettingsBag()
    {
        if (null === $this->settings) {
            $this->settings = new ParameterBag([
                self::SETTINGS_FIELD_LABELS => $this->getLabels()->toArray()
            ]);
        }

        return $this->settings;
    }
}
