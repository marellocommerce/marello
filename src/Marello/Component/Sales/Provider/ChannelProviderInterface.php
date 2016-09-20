<?php

namespace Marello\Component\Sales\Provider;

use Doctrine\Common\Persistence\ObjectManager;

use Marello\Component\Product\Entity\Product;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Component\Product\Model\ProductInterface;
use Marello\Component\Sales\SalesChannelInterface;

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
