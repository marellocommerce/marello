<?php

namespace Marello\Bundle\CustomerBundle\Entity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Marello\Bundle\CustomerBundle\Entity\Customer;

class CustomerRepository extends ServiceEntityRepository
{
    public function isExistCustomerByEmailAndOrganization(Customer $customer): bool
    {
        $qb = $this->createQueryBuilder('c');
        $qb
            ->select('1')
            ->where($qb->expr()->eq('LOWER(c.email)', ':email'))
            ->setParameter('email', mb_strtolower($customer->getEmail()));

        if ($customer->getOrganization()) {
            $qb->andWhere($qb->expr()->eq('c.organization', ':organization'))
                ->setParameter('organization', $customer->getOrganization());
        }

        return (bool) $qb->getQuery()->getScalarResult();
    }
}
