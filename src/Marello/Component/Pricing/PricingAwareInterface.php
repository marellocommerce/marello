<?php

namespace Marello\Component\Pricing;

use Doctrine\Common\Collections\ArrayCollection;
use Marello\Component\Product\ProductChannelPriceInterface;
use Marello\Component\Product\ProductPriceInterface;

interface PricingAwareInterface
{
    const CHANNEL_PRICING_STATE_KEY = 'pricing_enabled';
    const CHANNEL_PRICING_DROP_KEY = 'pricing_drop';

    /**
     * Get collection of ProductChannelPrices
     * @return ArrayCollection
     */
    public function getChannelPrices();

    /**
     * Add channel price to collection
     * @param ProductChannelPriceInterface $channelPrice
     * @return mixed
     */
    public function addChannelPrice(ProductChannelPriceInterface $channelPrice);

    /**
     * Remove channel price from collection
     * @param ProductChannelPriceInterface $channelPrice
     * @return mixed
     */
    public function removeChannelPrice(ProductChannelPriceInterface $channelPrice);

    /** @return bool */
    public function hasChannelPrices();

    /**
     * Get collection of ProductPrices
     * @return ArrayCollection
     */
    public function getPrices();

    /**
     * Add price to collection
     * @param ProductPriceInterface $price
     * @return mixed
     */
    public function addPrice(ProductPriceInterface $price);

    /**
     * Remove price from collection
     * @param ProductPriceInterface $price
     * @return mixed
     */
    public function removePrice(ProductPriceInterface $price);

    /** @return bool */
    public function hasPrices();
}
