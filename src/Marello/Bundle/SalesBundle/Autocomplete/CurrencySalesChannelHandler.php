<?php

namespace Marello\Bundle\SalesBundle\Autocomplete;

use Oro\Bundle\FormBundle\Autocomplete\SearchHandler;
use Doctrine\ORM\QueryBuilder;

class CurrencySalesChannelHandler extends SearchHandler
{

    /**
     * @param string $currency
     * @return QueryBuilder
     */
    protected function getBasicQueryBuilder($currency)
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('sc');
        $queryBuilder->andWhere('sc.currency = :currency')
            ->setParameter('currency', (int) $currency);

        return $queryBuilder;
    }
}