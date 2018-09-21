<?php

namespace Marello\Bundle\AddressBundle\Tests\Stubs;

use Marello\Bundle\AddressBundle\Entity\MarelloAddress;

class AddressStub extends MarelloAddress
{
    /**
     * @var string
     */
    protected $regionCode;

    /**
     * @var string
     */
    protected $street2;

    /**
     * @param string $street2
     */
    public function __construct($street2 = null)
    {
        $this->street2 = $street2;
    }

    /**
     * @param string $code
     * @return AddressStub
     */
    public function setRegionCode($code)
    {
        $this->regionCode = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return 'Formatted';
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return 'User';
    }

    /**
     * @return string
     */
    public function getMiddleName()
    {
        return 'Name';
    }

    /**
     * @return string
     */
    public function getNamePrefix()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getNameSuffix()
    {
        return '';
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return '1 Tests str.';
    }

    /**
     * Get street2
     *
     * @return string
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return 'New York';
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegionName()
    {
        return 'New York';
    }

    /**
     * Get region code.
     *
     * @return string
     */
    public function getRegionCode()
    {
        return $this->regionCode;
    }

    /**
     * Get postal_code
     *
     * @return string
     */
    public function getPostalCode()
    {
        return '12345';
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountryName()
    {
        return 'United States';
    }

    /**
     * Get country ISO3 code.
     *
     * @return string
     */
    public function getCountryIso3()
    {
        return 'USA';
    }

    /**
     * Get country ISO2 code.
     *
     * @return string
     */
    public function getCountryIso2()
    {
        return 'US';
    }

    /**
     * Get organization.
     *
     * @return string
     */
    public function getOrganization()
    {
        return 'Company Ltd.';
    }
}
