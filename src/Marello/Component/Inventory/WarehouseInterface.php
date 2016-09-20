<?php

namespace Marello\Component\Inventory;

use Marello\Component\Address\Model\AddressInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;

interface WarehouseInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label);

    /**
     * @return boolean
     */
    public function isDefault();

    /**
     * @param boolean $default
     *
     * @return $this
     */
    public function setDefault($default);

    /**
     * @return OrganizationInterface
     */
    public function getOwner();

    /**
     * @param OrganizationInterface $owner
     *
     * @return $this
     */
    public function setOwner(OrganizationInterface $owner);

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return AddressInterface
     */
    public function getAddress();

    /**
     * @param AddressInterface $address
     *
     * @return $this
     */
    public function setAddress(AddressInterface $address);
}
