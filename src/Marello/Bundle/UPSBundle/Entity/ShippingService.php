<?php

namespace Marello\Bundle\UPSBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\AddressBundle\Entity\Country;

use Marello\Bundle\UPSBundle\Entity\Repository\ShippingServiceRepository;

#[ORM\Table(name: 'marello_ups_shipping_service')]
#[ORM\Entity(repositoryClass: ShippingServiceRepository::class)]
class ShippingService
{
    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'code', type: Types::STRING, length: 10)]
    protected $code;

    /**
     * @var string
     */
    #[ORM\Column(name: 'description', type: Types::STRING, length: 255)]
    protected $description;

    /**
     * @var Country
     */
    #[ORM\JoinColumn(name: 'country_code', referencedColumnName: 'iso2_code', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Country::class)]
    protected $country;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     * @return $this
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getDescription();
    }
}
