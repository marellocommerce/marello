<?php

namespace Marello\Component\Product\Model;

use Doctrine\Common\Collections\Collection;

interface PricingAwareInterface
{
    const CHANNEL_PRICING_STATE_KEY = 'pricing_enabled';
    const CHANNEL_PRICING_DROP_KEY = 'pricing_drop';

    /**
     * Get collection of ProductChannelPrices
     * @return Collection
     */
    public function getChannelPrices();

    /**
     * Add channel price to collection
     * @param ProductChannelPriceInterface $channelPrice
     * @return PricingAwareInterface
     */
    public function addChannelPrice(ProductChannelPriceInterface $channelPrice);

    /**
     * Remove channel price from collection
     * @param ProductChannelPriceInterface $channelPrice
     * @return PricingAwareInterface
     */
    public function removeChannelPrice(ProductChannelPriceInterface $channelPrice);

    /**
     * @return bool
     */
    public function hasChannelPrices();

    /**
     * Get collection of ProductPrices
     * @return Collection
     */
    public function getPrices();

    /**
     * Add price to collection
     * @param ProductPriceInterface $price
     * @return PricingAwareInterface
     */
    public function addPrice(ProductPriceInterface $price);

    /**
     * Remove price from collection
     * @param ProductPriceInterface $price
     * @return PricingAwareInterface
     */
    public function removePrice(ProductPriceInterface $price);

    /**
     * @return bool
     */
    public function hasPrices();
}
