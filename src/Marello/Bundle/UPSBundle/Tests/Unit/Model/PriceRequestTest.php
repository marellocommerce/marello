<?php

namespace Marello\Bundle\UPSBundle\Tests\Unit\Model;

use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Marello\Bundle\AddressBundle\Tests\Stubs\AddressStub;
use Marello\Bundle\UPSBundle\Model\Package;
use Marello\Bundle\UPSBundle\Model\Request\PriceRequest;

class PriceRequestTest extends \PHPUnit_Framework_TestCase
{
    use EntityTestCaseTrait;

    const USERNAME = 'user';
    const PASSWORD = 'password';
    const ACCESS_LICENSE_NUMBER = '123';
    const REQUEST_OPTION = 'Rate';
    const SERVICE_CODE = '02';
    const SERVICE_DESCRIPTION = 'Service Code Description';
    const SHIPPER_NAME = 'Shipper Name';
    const SHIPPER_NUMBER = '123';
    const SHIP_FROM_NAME = 'Ship From Name';
    const SHIP_TO_NAME = 'Ship To Name';

    /**
     * @var PriceRequest
     */
    protected $model;

    /**
     * @var  array
     */
    protected $resultArray;

    protected function setUp()
    {
        $this->model = new PriceRequest();
    }

    protected function tearDown()
    {
        unset($this->model);
    }

    public function testAccessors()
    {
        static::assertPropertyAccessors(
            $this->model,
            [
                ['username', self::USERNAME],
                ['password', self::PASSWORD],
                ['accessLicenseNumber', self::ACCESS_LICENSE_NUMBER],
                ['requestOption', self::REQUEST_OPTION],
                ['serviceDescription', self::SERVICE_DESCRIPTION],
                ['serviceCode', self::SERVICE_CODE],
                ['shipperName', self::SHIPPER_NAME],
                ['shipperNumber', self::SHIPPER_NUMBER],
                ['shipperAddress', new AddressStub()],
                ['shipFromName', self::SHIP_FROM_NAME],
                ['shipFromAddress', new AddressStub()],
                ['shipToName', self::SHIP_TO_NAME],
                ['shipToAddress', new AddressStub()],
            ]
        );
    }

    public function testStringify()
    {
        $this->initPriceRequest();
        static::assertEquals(json_encode($this->resultArray), $this->model->stringify());
    }

    public function initPriceRequest()
    {
        $package = (new Package())
            ->setPackagingTypeCode(PackageTest::PACKAGING_TYPE_CODE)
            ->setDimensionCode(PackageTest::DIMENSION_CODE)
            ->setDimensionLength(PackageTest::DIMENSION_LENGTH)
            ->setDimensionWidth(PackageTest::DIMENSION_WIDTH)
            ->setDimensionHeight(PackageTest::DIMENSION_HEIGHT)
            ->setWeightCode(PackageTest::WEIGHT_CODE)
            ->setWeight(PackageTest::WEIGHT);
        $address = new AddressStub();
        $this->model
            ->setSecurity(self::USERNAME, self::PASSWORD, self::ACCESS_LICENSE_NUMBER)
            ->setRequestOption(self::REQUEST_OPTION)
            ->setService(self::SERVICE_CODE, self::SERVICE_DESCRIPTION)
            ->setShipper(self::SHIPPER_NAME, self::SHIPPER_NUMBER, $address)
            ->setShipFrom(self::SHIP_FROM_NAME, $address)
            ->setShipTo(self::SHIP_TO_NAME, $address)
            ->addPackage($package);

        $addressArray = [
            'AddressLine'       => [
                    $address->getStreet(),
                    $address->getStreet2()
                ],
            'City'              => $address->getCity(),
            'StateProvinceCode' => $address->getRegionCode(),
            'PostalCode'        => $address->getPostalCode(),
            'CountryCode'       => $address->getCountryIso2(),
        ];
        $this->resultArray = [
            'UPSSecurity' => [
                    'UsernameToken'      => [
                            'Username' => self::USERNAME,
                            'Password' => self::PASSWORD,
                        ],
                    'ServiceAccessToken' => [
                            'AccessLicenseNumber' => self::ACCESS_LICENSE_NUMBER,
                        ],
                ],
            'RateRequest' => [
                    'Request'  => [
                            'RequestOption' => self::REQUEST_OPTION,
                        ],
                    'Shipment' => [
                            'Shipper'  => [
                                    'Name'          => self::SHIPPER_NAME,
                                    'ShipperNumber' => self::SHIPPER_NUMBER,
                                    'Address'       => $addressArray,
                                ],
                            'ShipTo'   => [
                                    'Name'    => self::SHIP_TO_NAME,
                                    'Address' => $addressArray,
                                ],
                            'ShipFrom' => [
                                    'Name'    => self::SHIP_FROM_NAME,
                                    'Address' => $addressArray,
                                ],
                            'Package'  => [$package->toArray()],
                            'Service'  => [
                                'Code'        => self::SERVICE_CODE,
                                'Description' => self::SERVICE_DESCRIPTION,
                            ],
                        ],
                ],
        ];
    }
}
