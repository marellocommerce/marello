<?php

namespace Marello\Bundle\ProductBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;

/**
 * Class ProductStatus
 * @package Marello\Bundle\ProductBundle\Entity
 */
#[ORM\Table(name: 'marello_product_product_status')]
#[ORM\Entity]
#[Oro\Config(defaultValues: ['grouping' => ['groups' => ['dictionary']]])]
class ProductStatus
{
    const ENABLED = 'enabled';
    const DISABLED = 'disabled';
    
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
     * Set address type label
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
