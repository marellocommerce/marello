<?php

namespace Marello\Bundle\PricingBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;

#[ORM\Table(name: 'marello_pricing_price_type')]
#[ORM\Entity]
#[Oro\Config(defaultValues: ['dataaudit' => ['auditable' => true]])]
class PriceType
{
    /**
     * @var string
     */
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING)]
    protected $name;

    /**
     * @var string
     */
    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $label;

    /**
     * @param string $name
     * @param string $label
     */
    public function __construct($name, $label)
    {
        $this->name = $name;
        $this->label = $label;
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
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }
}
