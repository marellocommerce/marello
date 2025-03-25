<?php

namespace Marello\Bundle\TicketBundle\Provider;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\DashboardBundle\Model\WidgetOptionBag;
use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\EntityExtendBundle\Entity\Repository\EnumValueRepository;

use Marello\Bundle\TicketBundle\Entity\Ticket;

class DashboardDataProvider
{
    public function __construct(
        protected DoctrineHelper $doctrineHelper,
        protected AclHelper $aclHelper
    ) {
    }

    /**
     * @param WidgetOptionBag $widgetOptions
     * @return array
     */
    public function getTicketsStatusData(WidgetOptionBag $widgetOptions)
    {
        $dateRange = $widgetOptions->get('dateRange');
        $statuses = $widgetOptions->get('statuses') ?? [];
        $items = $this->doctrineHelper
            ->getEntityRepositoryForClass(Ticket::class)
            ->getTicketsStatusData($dateRange, $statuses) ?? [];

        $className = ExtendHelper::buildEnumValueClassName('marello_ticket_status');
        /** @var EnumValueRepository $enumRepo */
        $enumRepo = $this->doctrineHelper->getEntityRepositoryForClass($className);

        if (!empty($items)) {
            foreach ($items as $key => $item) {
                $status = $enumRepo->findOneBy(['id' => $item['statusId']]);
                $items[$key]['status_label'] = $status->getName();
            }
        }

        return $items;
    }
}
