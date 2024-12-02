<?php

namespace Marello\Bundle\LocaleBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\LocaleBundle\Entity\Localization;

trait LocalizationTrait
{
    #[ORM\ManyToOne(targetEntity: Localization::class)]
    #[ORM\JoinColumn(name: 'localization_id', referencedColumnName: 'id', nullable: true)]
    protected $localization;

    /**
     * @return Localization
     */
    public function getLocalization()
    {
        return $this->localization;
    }

    /**
     * @param Localization $localization
     * @return $this
     */
    public function setLocalization(Localization $localization = null)
    {
        $this->localization = $localization;

        return $this;
    }
}
