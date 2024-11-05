<?php

namespace Marello\Bundle\SalesBundle\Autocomplete;

use Doctrine\ORM\QueryBuilder;
use Marello\Bundle\POSBundle\Migrations\Data\ORM\LoadSalesChannelPOSTypeData;
use Marello\Bundle\SalesBundle\Migrations\Data\ORM\LoadSalesChannelTypesData;

class SalesChannelForPosHandler extends AbstractMultiConditionSalesChannelHandler
{
    protected function getBasicQueryBuilder(): QueryBuilder
    {
        $qb = $this->entityRepository->createQueryBuilder('sc');

        return $qb
            ->innerJoin('sc.channelType', 'sct')
            ->where($qb->expr()->notIn('sct.name', ':excludedTypes'))
            ->setParameter('excludedTypes', [LoadSalesChannelTypesData::STORE, LoadSalesChannelPOSTypeData::POS])
            ->orderBy('sc.name', 'ASC');
    }
}
