<?php

namespace Marello\Bundle\TicketBundle\Entity\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

class TicketCategoryRepository extends ServiceEntityRepository
{
    /** @var AclHelper $aclHelper */
    private AclHelper $aclHelper;

    /**
     * @return array
     */
    public function getAllCategories()
    {
        $qb = $this->createQueryBuilder('c');

        $qb
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->aclHelper->apply($qb)->getResult();
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