<?php

namespace Marello\Component\Inventory\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Address\Entity\Address;
use Marello\Component\Address\Model\AddressInterface;
use Marello\Component\Inventory\Model\WarehouseInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;

/**
 * @Oro\Config(
 *      defaultValues={
 *          "security"={
 *              "type"="ACL",
 *              "group_name"=""
 *          },
 *          "ownership"={
 *              "owner_type"="ORGANIZATION",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="owner_id"
 *          }
 *      }
 * )
 */
class Warehouse implements WarehouseInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var bool
     */
    protected $default;

    /**
     * @var OrganizationInterface
     */
    protected $owner;

    /**
     * @var Address
     */
    protected $address = null;

    /**
     * @param string $label
     * @param bool   $default
     */
    public function __construct($label = null, $default = false)
    {
        $this->label   = $label;
        $this->default = $default;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * @param boolean $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * @return OrganizationInterface
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param OrganizationInterface $owner
     *
     * @return $this
     */
    public function setOwner(OrganizationInterface $owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->label;
    }

    /**
     * @return AddressInterface
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param AddressInterface $address
     *
     * @return $this
     */
    public function setAddress(AddressInterface $address)
    {
        $this->address = $address;

        return $this;
    }
}
