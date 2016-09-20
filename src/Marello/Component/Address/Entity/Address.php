<?php

namespace Marello\Component\Address\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Address\Model\AddressInterface;
use Oro\Bundle\AddressBundle\Entity\AbstractAddress;

class Address extends AbstractAddress implements AddressInterface
{
    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }
}
