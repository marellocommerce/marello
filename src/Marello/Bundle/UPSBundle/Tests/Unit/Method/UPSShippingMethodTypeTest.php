<?php

namespace Marello\Bundle\UPSBundle\Tests\Unit\Method;

use Oro\Bundle\AddressBundle\Entity\Country;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Marello\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Marello\Bundle\UPSBundle\Cache\ShippingPriceCache;
use Marello\Bundle\UPSBundle\Cache\ShippingPriceCacheKey;
use Marello\Bundle\UPSBundle\Entity\ShippingService;
use Marello\Bundle\UPSBundle\Entity\UPSSettings;
use Marello\Bundle\UPSBundle\Factory\PriceRequestFactory;
use Marello\Bundle\UPSBundle\Form\Type\UPSShippingMethodOptionsType;
use Marello\Bundle\UPSBundle\Method\UPSShippingMethodType;
use Marello\Bundle\UPSBundle\Model\Package;
use Marello\Bundle\UPSBundle\Model\Request\PriceRequest;
use Marello\Bundle\UPSBundle\Model\Response\PriceResponse;
use Marello\Bundle\UPSBundle\Provider\UPSTransport as UPSTransportProvider;
use Oro\Component\Testing\Unit\EntityTrait;

class UPSShippingMethodTypeTest extends \PHPUnit_Framework_TestCase
{
    use EntityTrait;

    /**
     * @internal
     */
    const IDENTIFIER = '02';

    /**
     * @internal
     */
    const LABEL = 'service_code_label';

    /**
     * @var string
     */
    protected $methodId = 'shipping_method';

    /**
     * @var UPSSettings|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transport;

    /**
     * @var UPSTransportProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transportProvider;

    /**
     * @var ShippingService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shippingService;

    /**
     * @var PriceRequestFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $priceRequestFactory;

    /**
     * @var UPSShippingMethodType
     */
    protected $upsShippingMethodType;

    /**
     * @var ShippingPriceCache|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cache;

    protected function setUp()
    {
        $this->transport = $this->getEntity(
            UPSSettings::class,
            [
                'upsApiUser' => 'some user',
                'upsApiPassword' => 'some password',
                'upsApiKey' => 'some key',
                'upsShippingAccountNumber' => 'some number',
                'upsShippingAccountName' => 'some name',
                'upsPickupType' => '01',
                'upsUnitOfWeight' => 'LPS',
                'upsCountry' => new Country('US'),
                'applicableShippingServices' => [new ShippingService()]
            ]
        );

        $this->transportProvider = $this->createMock(UPSTransportProvider::class);

        $this->shippingService = $this->createMock(ShippingService::class);

        /** @var PriceRequestFactory | \PHPUnit_Framework_MockObject_MockObject $priceRequestFactory */
        $this->priceRequestFactory = $this->createMock(PriceRequestFactory::class);

        $this->cache = $this->createMock(ShippingPriceCache::class);

        $this->upsShippingMethodType = new UPSShippingMethodType(
            self::IDENTIFIER,
            self::LABEL,
            $this->methodId,
            $this->shippingService,
            $this->transport,
            $this->transportProvider,
            $this->priceRequestFactory,
            $this->cache
        );
    }

    public function testGetOptionsConfigurationFormType()
    {
        static::assertEquals(
            UPSShippingMethodOptionsType::class,
            $this->upsShippingMethodType->getOptionsConfigurationFormType()
        );
    }

    public function testGetSortOrder()
    {
        static::assertEquals(0, $this->upsShippingMethodType->getSortOrder());
    }

    /**
     * @param int $methodSurcharge
     * @param int $typeSurcharge
     * @param int $expectedPrice
     *
     * @dataProvider calculatePriceDataProvider
     */
    public function testCalculatePrice($methodSurcharge, $typeSurcharge, $expectedPrice)
    {
        /** @var ShippingContextInterface|\PHPUnit_Framework_MockObject_MockObject $context **/
        $context = $this->createMock(ShippingContextInterface::class);

        $methodOptions = ['surcharge' => $methodSurcharge];
        $this->shippingService->expects(self::any())->method('getCode')->willReturn(self::IDENTIFIER);
        $typeOptions = ['surcharge' => $typeSurcharge];

        $priceRequest = $this->createMock(PriceRequest::class);
        $priceRequest->expects(self::once())->method('getPackages')->willReturn([new Package(), new Package()]);

        $this->priceRequestFactory->expects(self::once())->method('create')->willReturn($priceRequest);

        $responsePrice = Price::create(50, 'USD');

        $priceResponse = $this->createMock(PriceResponse::class);
        $priceResponse->expects(self::once())->method('getPriceByService')->willReturn($responsePrice);

        $this->transportProvider->expects(self::once())->method('getPriceResponse')->willReturn($priceResponse);

        $cacheKey = (new ShippingPriceCacheKey())->setTransport($this->transport)->setPriceRequest($priceRequest)
            ->setMethodId($this->methodId)->setTypeId($this->shippingService->getCode());

        $this->cache->expects(static::once())
            ->method('createKey')
            ->with($this->transport, $priceRequest, $this->methodId, $this->shippingService->getCode())
            ->willReturn($cacheKey);

        $this->cache->expects(static::once())
            ->method('containsPrice')
            ->with($cacheKey)
            ->willReturn(false);

        $this->cache->expects(static::once())
            ->method('savePrice')
            ->with($cacheKey, $responsePrice);

        $price = $this->upsShippingMethodType->calculatePrice($context, $methodOptions, $typeOptions);

        static::assertEquals(Price::create($expectedPrice, 'USD'), $price);
    }

    /**
     * @return array
     */
    public function calculatePriceDataProvider()
    {
        return [
            'TypeSurcharge' => [
                'methodSurcharge' => 0,
                'typeSurcharge' => 5,
                'expectedPrice' => 55
            ],
            'MethodSurcharge' => [
                'methodSurcharge' => 3,
                'typeSurcharge' => 0,
                'expectedPrice' => 53
            ],
            'Method&TypeSurcharge' => [
                'methodSurcharge' => 3,
                'typeSurcharge' => 5,
                'expectedPrice' => 58
            ],
            'NoSurcharge' => [
                'methodSurcharge' => 0,
                'typeSurcharge' => 0,
                'expectedPrice' => 50
            ]
        ];
    }

    public function testCalculatePriceCache()
    {
        /** @var ShippingContextInterface|\PHPUnit_Framework_MockObject_MockObject $context **/
        $context = $this->createMock(ShippingContextInterface::class);

        $methodOptions = ['surcharge' => 10];
        $this->shippingService->expects(self::any())->method('getCode')->willReturn('02');
        $typeOptions = ['surcharge' => 15];

        $priceRequest = $this->createMock(PriceRequest::class);
        $priceRequest->expects(self::once())->method('getPackages')->willReturn([new Package(), new Package()]);

        $this->priceRequestFactory->expects(self::once())->method('create')->willReturn($priceRequest);

        $this->transportProvider->expects(self::never())->method('getPriceResponse');

        $cacheKey = (new ShippingPriceCacheKey())->setTransport($this->transport)->setPriceRequest($priceRequest)
            ->setMethodId($this->methodId)->setTypeId($this->shippingService->getCode());

        $this->cache->expects(static::once())
            ->method('createKey')
            ->with($this->transport, $priceRequest, $this->methodId, $this->shippingService->getCode())
            ->willReturn($cacheKey);

        $this->cache->expects(static::once())
            ->method('containsPrice')
            ->with($cacheKey)
            ->willReturn(true);

        $this->cache->expects(static::once())
            ->method('fetchPrice')
            ->with($cacheKey)
            ->willReturn(Price::create(5, 'USD'));

        $price = $this->upsShippingMethodType->calculatePrice($context, $methodOptions, $typeOptions);

        static::assertEquals(Price::create(30, 'USD'), $price);
    }
}
