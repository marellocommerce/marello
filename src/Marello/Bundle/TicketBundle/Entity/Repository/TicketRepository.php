<?php

namespace Marello\Bundle\TicketBundle\Entity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

class TicketRepository extends ServiceEntityRepository
{
    /** @var AclHelper $aclHelper */
    private AclHelper $aclHelper;

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
     * @param AclHelper $aclHelper
     * @return void
     */
    public function setAclHelper(AclHelper $aclHelper): void
    {
        $this->aclHelper = $aclHelper;
    }
}
