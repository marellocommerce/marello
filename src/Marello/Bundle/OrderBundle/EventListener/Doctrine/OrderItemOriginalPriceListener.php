<?php

namespace Marello\Bundle\OrderBundle\EventListener\Doctrine;

use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\TaxBundle\Calculator\TaxCalculatorInterface;
use Marello\Bundle\TaxBundle\Matcher\TaxRuleMatcherInterface;
use Marello\Bundle\TaxBundle\Model\ResultElement;

class OrderItemOriginalPriceListener
{
    public function __construct(
        protected TaxRuleMatcherInterface $taxRuleMatcher,
        protected TaxCalculatorInterface $taxCalculator
    ) {
    }

    public function prePersist(OrderItem $orderItem): void
    {
        $taxResultElement = $this->getCalculatedPriceValue($orderItem);
        $orderItem->setOriginalPriceInclTax($taxResultElement->getIncludingTax());
        $orderItem->setOriginalPriceExclTax($taxResultElement->getExcludingTax());
    }

    private function getCalculatedPriceValue(OrderItem $orderItem): ResultElement
    {
        $channel = $orderItem->getOrder()->getSalesChannel();
        $priceList = $orderItem->getProduct()->getSalesChannelPrice($channel);

        $taxRule = $this->taxRuleMatcher->match(
            [$orderItem->getTaxCode()->getCode()],
            $orderItem->getOrder(),
            $orderItem->getOrder()->getShippingAddress()
        );
        $taxRate = $taxRule ? $taxRule->getTaxRate()->getRate() : 0;

        return $this->taxCalculator->calculate($priceList->getDefaultPrice()->getValue(), $taxRate);
    }
}
