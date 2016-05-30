<?php

namespace Marello\Component\Address;

use Oro\Bundle\FormBundle\Entity\EmptyItem;
use Oro\Bundle\LocaleBundle\Model\FullNameInterface;
use Oro\Bundle\LocaleBundle\Model\AddressInterface as OroAddressInterface;

interface AddressInterface extends EmptyItem, FullNameInterface, OroAddressInterface
{
    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getPhone();

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone);
}