<?php

namespace Marello\Bundle\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

/**
 * Class ProductStatus
 * @package Marello\Bundle\ProductBundle\Entity
 *
 * @Oro\Config(
 *      defaultValues={
 *          "grouping"={
 *              "groups"={"dictionary"}
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_product_product_status')]
#[ORM\Entity]
class ProductStatus
{
    const ENABLED = 'enabled';
    const DISABLED = 'disabled';
    
    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "identity"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'name', type: 'string', length: 32)]
    #[ORM\Id]
    protected $name;

    #[ORM\Column(name: 'label', type: 'string', length: 255, unique: true)]
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
