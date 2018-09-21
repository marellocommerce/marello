<?php

namespace Marello\Bundle\UPSBundle\Tests\Unit\Cache;

use Doctrine\Common\Cache\CacheProvider;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Marello\Bundle\UPSBundle\Cache\Lifetime\LifetimeProviderInterface;
use Marello\Bundle\UPSBundle\Cache\ShippingPriceCache;
use Marello\Bundle\UPSBundle\Cache\ShippingPriceCacheKey;
use Marello\Bundle\UPSBundle\Entity\UPSSettings;

class ShippingPriceCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @internal
     */
    const CACHE_KEY = 'cache_key';

    /**
     * @internal
     */
    const SETTINGS_ID = 7;

    /**
     * @var ShippingPriceCache
     */
    private $cache;

    /**
     * @var CacheProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheProvider;

    /**
     * @var LifetimeProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $lifetimeProvider;

    /**
     * @var UPSSettings|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;

    /**
     * @var ShippingPriceCacheKey|\PHPUnit_Framework_MockObject_MockObject
     */
    private $cacheKey;

    public function setUp()
    {
        $this->cacheProvider = $this->createMock(CacheProvider::class);
        $this->lifetimeProvider = $this->createMock(LifetimeProviderInterface::class);

        $this->settings = $this->createMock(UPSSettings::class);

        $this->settings
            ->method('getId')
            ->willReturn(self::SETTINGS_ID);

        $this->cacheProvider->expects(static::once())
            ->method('setNamespace')
            ->with('marello_ups_shipping_price_'.self::SETTINGS_ID);

        $this->cacheKey = $this->getCacheKeyMock($this->settings, self::CACHE_KEY);

        $this->lifetimeProvider->method('generateLifetimeAwareKey')
            ->with($this->settings, self::CACHE_KEY)
            ->willReturn(self::CACHE_KEY);

        $this->cache = new ShippingPriceCache($this->cacheProvider, $this->lifetimeProvider);
    }

    public function testFetchPrice()
    {
        $this->cacheProvider->expects(static::once())
            ->method('contains')
            ->with(self::CACHE_KEY)
            ->willReturn(true);

        $price = Price::create(10, 'USD');

        $this->cacheProvider->expects(static::once())
            ->method('fetch')
            ->with(self::CACHE_KEY)
            ->willReturn($price);

        static::assertSame($price, $this->cache->fetchPrice($this->cacheKey));
    }

    public function testFetchPriceFalse()
    {
        $this->cacheProvider->expects(static::once())
            ->method('contains')
            ->with(self::CACHE_KEY)
            ->willReturn(false);

        $this->cacheProvider->expects(static::never())
            ->method('fetch');

        static::assertFalse($this->cache->fetchPrice($this->cacheKey));
    }

    public function testContainsPrice()
    {
        $this->cacheProvider->expects(static::once())
            ->method('contains')
            ->with(self::CACHE_KEY)
            ->willReturn(true);

        static::assertTrue($this->cache->containsPrice($this->cacheKey));
    }

    public function testContainsPriceFalse()
    {
        $this->cacheProvider->expects(static::once())
            ->method('contains')
            ->with(self::CACHE_KEY)
            ->willReturn(false);

        static::assertFalse($this->cache->containsPrice($this->cacheKey));
    }

    public function testSavePrice()
    {
        $lifetime = 100;

        $this->lifetimeProvider->method('getLifetime')
            ->with($this->settings, 86400)
            ->willReturn($lifetime);

        $price = Price::create(10, 'USD');
        $this->cacheProvider->expects(static::once())
            ->method('save')
            ->with(self::CACHE_KEY, $price, $lifetime);

        static::assertEquals($this->cache, $this->cache->savePrice($this->cacheKey, $price));
    }

    /**
     * @param UPSSettings $settings
     * @param string      $stringKey
     *
     * @return ShippingPriceCacheKey|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getCacheKeyMock(UPSSettings $settings, string $stringKey): ShippingPriceCacheKey
    {
        $mock = $this->createMock(ShippingPriceCacheKey::class);

        $mock->method('getTransport')
            ->willReturn($settings);

        $mock->method('generateKey')
            ->willReturn($stringKey);

        return $mock;
    }
}
