<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;

/**
 * Class WarehouseType
 * @package Marello\Bundle\InventoryBundle\Entity
 */
#[ORM\Table(name: 'marello_inventory_wh_type')]
#[ORM\Entity]
#[Oro\Config(defaultValues: ['grouping' => ['groups' => ['dictionary']]])]
class WarehouseType
{
    #[ORM\Id]
    #[ORM\Column(name: 'name', type: Types::STRING, length: 32)]
    #[Oro\ConfigField(defaultValues: ['importexport' => ['identity' => true]])]
    protected $name;

    #[ORM\Column(name: 'label', type: Types::STRING, length: 255, unique: true)]
    protected $label;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get type name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set warehouse type label
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get service type label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->label;
    }
}
