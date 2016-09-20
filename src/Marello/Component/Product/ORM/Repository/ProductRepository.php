<?php

namespace Marello\Component\Product\ORM\Repository;

use Doctrine\ORM\EntityRepository;

use Marello\Component\Product\Entity\Product;
use Marello\Component\Product\Repository\ProductRepositoryInterface;

class ProductRepository extends EntityRepository implements ProductRepositoryInterface
{
    /**
     * Return products for specified price list and product IDs
     *
     * @param int $salesChannel
     * @param array $productIds
     *
     * @return Product[]
     */
    public function findBySalesChannel($salesChannel, array $productIds)
    {
        if (!$productIds) {
            return [];
        }

        $qb = $this->createQueryBuilder('product');
        $qb
            ->where(
                $qb->expr()->isMemberOf(':salesChannel', 'product.channels'),
                $qb->expr()->in('product.id', ':productIds')
            )
            ->setParameter('salesChannel', $salesChannel)
            ->setParameter('productIds', $productIds);

        return $qb->getQuery()->getResult();
    }
}
