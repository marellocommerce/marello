<?php

namespace Marello\Component\Pricing\ORM\Provider;

interface ChannelPriceProviderInterface
{
    /**
     * Get prices for each channel or get default price.
     * if no price is available, it is not sold in the selected channel.
     * @param $channel
     * @param array $products
     * @return array
     */
    public function getPrices($channel, array $products);

    /**
     * Get channel price
     * @param $channel
     * @param $product
     * @return array $data
     */
    public function getChannelPrice($channel, $product);

    /**
     * Get Default price by currency for product
     * @param $channel
     * @param $product
     * @return float
     */
    public function getDefaultPrice($channel, $product);
}
