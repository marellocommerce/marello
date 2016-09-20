<?php

namespace Marello\Component\Pricing\Repository;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;

use Marello\Component\Product\Model\ProductChannelPriceInterface;

interface ProductChannelPriceRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * Return product prices for specified channel and productId
     *
     * @param int $salesChannel
     * @param int $productId
     *
     * @return ProductChannelPriceInterface[]
     */
    public function findOneBySalesChannel($salesChannel, $productId);
}
