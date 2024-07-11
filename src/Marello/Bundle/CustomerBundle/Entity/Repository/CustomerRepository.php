<?php

namespace Marello\Bundle\CustomerBundle\Entity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\MarelloOrderBundle;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Marello\Bundle\OrderBundle\Entity\Repository\OrderRepository;

class CustomerRepository extends ServiceEntityRepository
{
    /**
     * @param AclHelper $aclHelper
     * @param \DateTime|null $start
     * @param \DateTime|null $end
     *
     * @return int
     */
    public function getTotalCustomersNumberValue(
        AclHelper $aclHelper,
        \DateTime $start = null,
        \DateTime $end = null
    ) {
        $qb = $this->createQueryBuilder('c');
        $qb->select('count(c.id) as val');
        if ($start && $end) {
            $qb->andWhere($qb->expr()->between('c.createdAt', ':dateStart', ':dateEnd'))
                ->setParameter('dateStart', $start)
                ->setParameter('dateEnd', $end);
        } elseif ($start) {
            $qb
                ->andWhere($qb->expr()->gte('c.createdAt', ':dateStart'))
                ->setParameter('dateStart', $start);
        } elseif ($end) {
            $qb
                ->andWhere($qb->expr()->lt('c.createdAt', ':dateEnd'))
                ->setParameter('dateEnd', $end);
        }
        $value = $aclHelper->apply($qb)->getOneOrNullResult();

        return $value['val'] ?: 0;
    }

    /**
     * @param AclHelper $aclHelper
     * @param \DateTime|null $start
     * @param \DateTime|null $end
     *
     * @return int
     */
    public function getTotalCustomersWithNewOrderValue(
        AclHelper $aclHelper,
        \DateTime $start = null,
        \DateTime $end = null
    ) {
        $qb = $this->createQueryBuilder('c');
        $qb->select('count(DISTINCT c.id) as val')
            ->innerJoin(Order::class, 'o', Join::WITH, 'o.customer = c.id');
        if ($start && $end) {
            $qb->andWhere($qb->expr()->between('o.purchaseDate', ':dateStart', ':dateEnd'))
                ->setParameter('dateStart', $start)
                ->setParameter('dateEnd', $end);
        } elseif ($start) {
            $qb
                ->andWhere($qb->expr()->gte('o.purchaseDate', ':dateStart'))
                ->setParameter('dateStart', $start);
        } elseif ($end) {
            $qb
                ->andWhere($qb->expr()->lt('o.purchaseDate', ':dateEnd'))
                ->setParameter('dateEnd', $end);
        }

        // Subquery to check for orders before the start date
        if ($start) {
            $subQb = $this->createQueryBuilder('c2')
                ->select('1')
                ->innerJoin(Order::class, 'o2', Join::WITH, 'o2.customer = c2.id')
                ->where('c2.id = c.id')
                ->andWhere('o2.purchaseDate < :dateStart')
                ->setParameter('dateStart', $start);

            $qb->andWhere($qb->expr()->exists($subQb->getDQL()));
        }

        $value = $aclHelper->apply($qb)->getOneOrNullResult();
        var_dump($value['val'] ?: 0);
        return $value['val'] ?: 0;
    }
}