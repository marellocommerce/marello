<?php

namespace Marello\Bundle\OrderBundle\EventListener\Doctrine;

use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\PricingBundle\DependencyInjection\Configuration;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;

class OrderItemOriginalPriceListener
{
    public function __construct(
        protected ConfigManager $configManager
    ) {}

    public function prePersist(OrderItem $orderItem): void
    {
        $this->setDefaultPrice($orderItem);
    }

    private function setDefaultPrice(OrderItem $orderItem): void
    {
        $channel = $orderItem->getOrder()->getSalesChannel();
        $priceList = $orderItem->getProduct()->getSalesChannelPrice($channel);
        if ($this->configManager->get(Configuration::VAT_SYSTEM_CONFIG_PATH)) {
            $orderItem->setOriginalPriceInclTax($priceList->getDefaultPrice()->getValue());
        } else {
            $orderItem->setOriginalPriceExclTax($priceList->getDefaultPrice()->getValue());
        }
    }
}
