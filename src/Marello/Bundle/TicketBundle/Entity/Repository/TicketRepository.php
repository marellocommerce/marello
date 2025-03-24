<?php

namespace Marello\Bundle\TicketBundle\Entity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\DashboardBundle\Filter\DateFilterProcessor;

class TicketRepository extends ServiceEntityRepository
{
    /** @var AclHelper $aclHelper */
    private AclHelper $aclHelper;

    /**
     * @var DateFilterProcessor
     */
    protected $dateFilterProcessor;

    /**
     * @param DateFilterProcessor $dateFilterProcessor
     */
    public function setDateFilterProcessor(DateFilterProcessor $dateFilterProcessor)
    {
        $this->dateFilterProcessor = $dateFilterProcessor;
    }

    /**
     * @param User $user
     * @param int $limit
     * @param array $statuses
     * @return float|int|mixed|string
     */
    public function getTicketsAssignedTo(User $user, int $limit, array $statuses)
    {
        $qb = $this->createQueryBuilder('t');

        $qb
            ->where(
                $qb->expr()->eq('t.assignedTo', ':user')
            )
            ->orderBy('t.createdAt', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->setParameter('user', $user);

        if ($statuses) {
            $qb->andWhere($qb->expr()->in('t.ticketStatus', ':statuses'))
                ->setParameter('statuses', $statuses);
        }

        return $this->aclHelper->apply($qb->getQuery())->execute();
    }

    /**
     * @param int $quantity
     * @param array $dateRange
     *
     * @return array
     */
    public function getTicketsStatusData(array $dateRange, array $statuses = [])
    {
        $select = 'COUNT(t.id) as totalTickets, IDENTITY(t.ticketStatus) as statusId';
        $qb     = $this->createQueryBuilder('t');
        $qb
            ->select($select)
            ->groupBy('statusId')
            ->setMaxResults(5);
        $this->dateFilterProcessor->applyDateRangeFilterToQuery($qb, $dateRange, 't.createdAt');

        if (!empty($statuses)) {
            $qb->andWhere($qb->expr()->in('t.ticketStatus', ':statuses'))
                ->setParameter('statuses', $statuses);
        }

        return $this->aclHelper->apply($qb)->getArrayResult();
    }

    /**
     * @param AclHelper $aclHelper
     * @return void
     */
    public function setAclHelper(AclHelper $aclHelper): void
    {
        $this->aclHelper = $aclHelper;
    }
}
