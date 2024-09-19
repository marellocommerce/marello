<?php

namespace Marello\Bundle\CustomerBundle\Provider;

use Marello\Bundle\CustomerBundle\Entity\Repository\CustomerRepository;
use Oro\Bundle\DashboardBundle\Model\WidgetOptionBag;
use Oro\Bundle\DashboardBundle\Provider\BigNumber\BigNumberDateHelper;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

class CustomerMetricsProvider
{
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected BigNumberDateHelper $dateHelper,
        protected AclHelper $aclHelper
    ) {
    }

    /**
     * @param array $dateRange
     * @param WidgetOptionBag $widgetOptions
     * @return int
     */
    public function getTotalCustomersNumberValues($dateRange, WidgetOptionBag $widgetOptions)
    {
        list($start, $end) = $this->dateHelper->getPeriod($dateRange, 'MarelloCustomerBundle:Customer', 'createdAt');
        return $this->customerRepository->getTotalCustomersNumberValue(
            $this->aclHelper,
            $start,
            $end
        );
    }

    /**
     * @param array $dateRange
     * @param WidgetOptionBag $widgetOptions
     * @return int
     */
    public function getCustomersRetentionValues($dateRange, WidgetOptionBag $widgetOptions)
    {
        list($start, $end) = $this->dateHelper->getPeriod($dateRange, 'MarelloOrderBundle:Order', 'purchaseDate');

        return $this->customerRepository->getTotalCustomersWithNewOrderValue(
            $this->aclHelper,
            $start,
            $end
        );
    }
}
