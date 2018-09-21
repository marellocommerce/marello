<?php

namespace Marello\Bundle\UPSBundle\Tests\Unit\Method\Factory;

use Doctrine\Common\Collections\Collection;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Oro\Bundle\IntegrationBundle\Entity\Channel;
use Oro\Bundle\IntegrationBundle\Generator\IntegrationIdentifierGeneratorInterface;
use Oro\Bundle\IntegrationBundle\Provider\IntegrationIconProviderInterface;
use Oro\Bundle\LocaleBundle\Helper\LocalizationHelper;

use Marello\Bundle\UPSBundle\Cache\ShippingPriceCache;
use Marello\Bundle\UPSBundle\Entity\ShippingService;
use Marello\Bundle\UPSBundle\Entity\UPSSettings;
use Marello\Bundle\UPSBundle\Factory\PriceRequestFactory;
use Marello\Bundle\UPSBundle\Method\Factory\UPSShippingMethodFactory;
use Marello\Bundle\UPSBundle\Method\Factory\UPSShippingMethodTypeFactoryInterface;
use Marello\Bundle\UPSBundle\Method\UPSShippingMethod;
use Marello\Bundle\UPSBundle\Method\UPSShippingMethodType;
use Marello\Bundle\UPSBundle\Provider\UPSTransport;

class UPSShippingMethodFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UPSTransport|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transport;

    /**
     * @var PriceRequestFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceRequestFactory;

    /**
     * @var LocalizationHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $localizationHelper;

    /**
     * @var ShippingPriceCache|\PHPUnit_Framework_MockObject_MockObject
     */
    private $shippingPriceCache;

    /**
     * @var IntegrationIdentifierGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $integrationIdentifierGenerator;

    /**
     * @var UPSShippingMethodTypeFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $methodTypeFactory;

    /**
     * @var IntegrationIconProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $integrationIconProvider;

    /**
     * @var UPSShippingMethodFactory
     */
    private $factory;

    /** @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject $dispatcher */
    private $dispatcher;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->transport = $this->createMock(UPSTransport::class);
        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->priceRequestFactory = $this->createMock(PriceRequestFactory::class);
        $this->localizationHelper = $this->createMock(LocalizationHelper::class);
        $this->shippingPriceCache = $this->createMock(ShippingPriceCache::class);
        $this->integrationIdentifierGenerator = $this->createMock(IntegrationIdentifierGeneratorInterface::class);
        $this->methodTypeFactory = $this->createMock(UPSShippingMethodTypeFactoryInterface::class);
        $this->integrationIconProvider = $this->createMock(IntegrationIconProviderInterface::class);

        $this->factory = new UPSShippingMethodFactory(
            $this->transport,
            $this->priceRequestFactory,
            $this->localizationHelper,
            $this->integrationIconProvider,
            $this->shippingPriceCache,
            $this->integrationIdentifierGenerator,
            $this->methodTypeFactory,
            $this->dispatcher
        );
    }

    public function testCreate()
    {
        $iconUri = 'bundles/icon-uri.png';
        $identifier = 'ups_1';
        $labelsCollection = $this->createMock(Collection::class);

        /** @var UPSSettings|\PHPUnit_Framework_MockObject_MockObject $settings */
        $settings = $this->createMock(UPSSettings::class);

        $settings->expects($this->once())
            ->method('getLabels')
            ->willReturn($labelsCollection);

        /** @var Channel|\PHPUnit_Framework_MockObject_MockObject $channel */
        $channel = $this->createMock(Channel::class);
        $channel->expects($this->any())
            ->method('getTransport')
            ->willReturn($settings);
        $channel->expects($this->any())
            ->method('isEnabled')
            ->willReturn(true);

        $this->integrationIconProvider
            ->expects(static::once())
            ->method('getIcon')
            ->with($channel)
            ->willReturn($iconUri);

        $type1 = $this->createMock(UPSShippingMethodType::class);
        $type2 = $this->createMock(UPSShippingMethodType::class);

        $service1 = $this->createMock(ShippingService::class);
        $service2 = $this->createMock(ShippingService::class);

        $this->methodTypeFactory->expects($this->at(0))
            ->method('create')
            ->with($channel, $service1)
            ->willReturn($type1);

        $this->methodTypeFactory->expects($this->at(1))
            ->method('create')
            ->with($channel, $service2)
            ->willReturn($type2);

        $serviceCollection = $this->createMock(Collection::class);
        $serviceCollection->expects($this->once())
            ->method('toArray')
            ->willReturn([$service1, $service2]);

        $settings->expects($this->once())
            ->method('getApplicableShippingServices')
            ->willReturn($serviceCollection);

        $this->integrationIdentifierGenerator
            ->expects($this->once())
            ->method('generateIdentifier')
            ->with($channel)
            ->willReturn($identifier);

        $this->localizationHelper->expects($this->once())
            ->method('getLocalizedValue')
            ->with($labelsCollection)
            ->willReturn('en');

        $this->assertEquals(new UPSShippingMethod(
            $identifier,
            'en',
            $iconUri,
            [$type1, $type2],
            $settings,
            $this->transport,
            $this->priceRequestFactory,
            $this->shippingPriceCache,
            true
        ), $this->factory->create($channel));
    }
}
