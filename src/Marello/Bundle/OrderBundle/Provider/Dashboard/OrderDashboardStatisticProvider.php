<?php

namespace Marello\Bundle\OrderBundle\Provider\Dashboard;

use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\DashboardBundle\Model\WidgetOptionBag;
use Oro\Bundle\DashboardBundle\Provider\BigNumber\BigNumberDateHelper;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Entity\Repository\OrderRepository;

class OrderDashboardStatisticProvider
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected BigNumberDateHelper $dateHelper,
        protected AclHelper $aclHelper
    ) {
    }

    /**
     * @param array $dateRange
     * @param WidgetOptionBag $widgetOptions
     * @return int
     */
    public function getTotalRevenueValues($dateRange, WidgetOptionBag $widgetOptions)
    {
        list($start, $end) = $this->dateHelper->getPeriod($dateRange, Order::class, 'createdAt');

        return $this->orderRepository->getTotalRevenueValue(
            $this->aclHelper,
            $start,
            $end,
            $widgetOptions->get('salesChannel')
        );
    }

    /**
     * @param array $dateRange
     * @param WidgetOptionBag $widgetOptions
     * @return int
     */
    public function getTotalOrdersNumberValues($dateRange, WidgetOptionBag $widgetOptions)
    {
        list($start, $end) = $this->dateHelper->getPeriod($dateRange, Order::class, 'createdAt');
        return $this->orderRepository->getTotalOrdersNumberValue(
            $this->aclHelper,
            $start,
            $end,
            $widgetOptions->get('salesChannel')
        );
    }

    /**
     * @param array $dateRange
     * @param WidgetOptionBag $widgetOptions
     * @return int
     */
    public function getAverageOrderValues($dateRange, WidgetOptionBag $widgetOptions)
    {
        list($start, $end) = $this->dateHelper->getPeriod($dateRange, Order::class, 'createdAt');

        return $this->orderRepository->getAverageOrderValue(
            $this->aclHelper,
            $start,
            $end,
            $widgetOptions->get('salesChannel')
        );
    }
}
