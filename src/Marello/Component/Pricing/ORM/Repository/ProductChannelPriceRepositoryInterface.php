<?php

namespace Marello\Component\Pricing\ORM\Repository;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;

use Marello\Component\Product\ProductChannelPriceInterface;

interface ProductChannelPriceRepository extends ObjectRepository, Selectable
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
