<?php

namespace Marello\Bundle\SalesBundle\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Event\OrmResultAfter;

class SalesChannelGroupDatagridListener
{
    /**
     * @deprecated will be removed in major
     * @param OrmResultAfter $event
     */
    public function onResultAfter(OrmResultAfter $event)
    {
        return;
    }
}
