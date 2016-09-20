<?php

namespace Marello\Bundle\OrderBundle\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\LocaleBundle\DQL\DQLNameFormatter;

class OrderGridAddressFilterListener
{
    /** @var DQLNameFormatter */
    protected $dqlNameFormatter;

    /**
     * @param DQLNameFormatter $dqlNameFormatter
     */
    public function __construct(DQLNameFormatter $dqlNameFormatter)
    {
        $this->dqlNameFormatter = $dqlNameFormatter;
    }

    /**
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();

        /*
         * Add generated entity name DQL to be selected under alias.
         * Aliases billingName and shippingName are added to query this way.
         */
        $config->offsetAddToArrayByPath('source.query.select', [
            $this->dqlNameFormatter->getFormattedNameDQL(
                'ba',
                'Marello\Component\Address\Entity\Address'
            ) . ' as billingName',
            $this->dqlNameFormatter->getFormattedNameDQL(
                'sa',
                'Marello\Component\Address\Entity\Address'
            ) . ' as shippingName',
        ]);
    }
}
