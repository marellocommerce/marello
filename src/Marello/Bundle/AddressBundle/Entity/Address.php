<?php

namespace Marello\Bundle\AddressBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Address\AddressInterface;
use Oro\Bundle\AddressBundle\Entity\AbstractAddress;

/**
 * @ORM\Entity
 * @ORM\Table(name="marello_address")
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(name="region",
 *          joinColumns={
 *              @ORM\JoinColumn(name="region_code", referencedColumnName="combined_code", nullable=true)
 *          }
 *      )
 * })
 */
class Address extends AbstractAddress implements AddressInterface
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=true)
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
