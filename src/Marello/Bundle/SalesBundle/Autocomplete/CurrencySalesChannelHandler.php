<?php

namespace Marello\Bundle\SalesBundle\Autocomplete;

use Oro\Bundle\FormBundle\Autocomplete\SearchHandler;
use Doctrine\ORM\QueryBuilder;

class CurrencySalesChannelHandler extends SearchHandler
{

    /**
     * {@inheritdoc}
     */
    protected function findById($query)
    {
        $parts = explode(';', $query);
        $entityIds = explode(',', $parts[0]);
        $currency = !empty($parts[1]) ? $parts[1] : false;

        $queryBuilder = $this->getBasicQueryBuilder($currency);
        $queryBuilder->andWhere($queryBuilder->expr()->in('sc.id', $entityIds));

        return $this->aclHelper->apply($queryBuilder->getQuery())->getResult();
    }

    /**
     * {@inheritdoc}
     */
    protected function searchEntities($search, $firstResult, $maxResults)
    {
        $parts = explode(';', $search);
        $searchString = $parts[0];
        $currency = !empty($parts[1]) ? $parts[1] : false;

        $queryBuilder = $this->getBasicQueryBuilder($currency);
        if ($searchString) {
            $this->addSearchCriteria($queryBuilder, $searchString);
        }
        $queryBuilder
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults);

        return $this->aclHelper->apply($queryBuilder->getQuery())->getResult();
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param $search
     */
    protected function addSearchCriteria(QueryBuilder $queryBuilder, $search)
    {
        $conditions = [];
        foreach ($this->getProperties() as $property) {
            $conditions[] = $queryBuilder->expr()->like(sprintf('sc.%s', $property), ':search');
        }
        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->orX()->addMultiple($conditions)
            )
            ->setParameter('search', '%' . str_replace(' ', '%', $search) . '%');
    }

    /**
     * @param int $currency
     * @return QueryBuilder
     */
    protected function getBasicQueryBuilder($currency)
    {
        $queryBuilder = $this->entityRepository->createQueryBuilder('sc');
        $queryBuilder->andWhere('sc.currency = :currency')
            ->setParameter('currency', (string) $currency);

        return $queryBuilder;
    }
}