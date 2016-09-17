<?php

namespace Marello\Component\Order\Entity\Repository;

use DateTime;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Persistence\ObjectRepository;

interface OrderRepositoryInterface extends ObjectRepository, Selectable
{
    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @param AclHelper $aclHelper
     *
     * @return int
     */
    public function getTotalRevenueValue(\DateTime $start, \DateTime $end, AclHelper $aclHelper);

    /**
     * @param \DateTime $start
     * @param \DateTime $end
     * @param AclHelper $aclHelper
     *
     * @return int
     */
    public function getTotalOrdersNumberValue(\DateTime $start, \DateTime $end, AclHelper $aclHelper);

    /**
     * get Average Order Amount by given period
     *
     * @param \DateTime $start
     * @param \DateTime $end
     * @param AclHelper $aclHelper
     *
     * @return int
     */
    public function getAverageOrderValue(\DateTime $start, \DateTime $end, AclHelper $aclHelper);
}
