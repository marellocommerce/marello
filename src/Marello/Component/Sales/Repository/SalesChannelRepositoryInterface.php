<?php

namespace Marello\Component\Sales\ORM\Repository;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;

interface SalesChannelRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * Return product prices for specified channel and productId
     *
     * @param int $salesChannel
     * @param int $productId
     *
     * @return int
     */
    public function findOneBySalesChannel($salesChannel, $productId);

    /**
     * Get excluded sales channel ids
     * @param array $relatedChannelIds
     * @return int[]
     */
    public function findExcludedSalesChannelIds(array $relatedChannelIds);
}
