<?php

namespace Marello\Component\Sales\Provider;

use Marello\Component\Product\Model\ProductInterface;

interface ChannelProviderInterface
{
    /**
     * Returns ids of all related sales channels for a product.
     *
     * @param ProductInterface $product
     *
     * @return array $ids
     */
    public function getSalesChannelsIds(ProductInterface $product);

    /**
     * Returns ids of all sales channels which are not in related to a product.
     *
     * @param ProductInterface $product
     *
     * @return array $ids
     */
    public function getExcludedSalesChannelsIds(ProductInterface $product);
}
