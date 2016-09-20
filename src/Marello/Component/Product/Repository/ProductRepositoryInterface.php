<?php

namespace Marello\Component\Product\Repository;

use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;

use Marello\Component\Product\Entity\Product;

interface ProductRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * Return products for specified price list and product IDs
     *
     * @param int $salesChannel
     * @param array $productIds
     *
     * @return Product[]
     */
    public function findBySalesChannel($salesChannel, array $productIds);
}
