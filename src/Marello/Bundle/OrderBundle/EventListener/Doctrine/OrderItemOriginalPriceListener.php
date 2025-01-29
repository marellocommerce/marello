<?php

namespace Marello\Bundle\OrderBundle\EventListener\Doctrine;

use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\TaxBundle\Model\ResultElement;
use Marello\Bundle\TaxBundle\Matcher\TaxRuleMatcherInterface;
use Marello\Bundle\TaxBundle\Calculator\TaxCalculatorInterface;

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
        $amount = 0;
        if ($orderItem->getProduct()) {
            $priceList = $orderItem->getProduct()->getSalesChannelPrice($channel);
            if ($priceList->getDefaultPrice()) {
                $amount = $priceList->getDefaultPrice()->getValue();
            }
        }

        $taxRule = $orderItem->getTaxCode() ? $this->taxRuleMatcher->match(
            [$orderItem->getTaxCode()->getCode()],
            $orderItem->getOrder(),
            $orderItem->getOrder()->getShippingAddress()
        ) : null;
        $taxRate = $taxRule ? $taxRule->getTaxRate()->getRate() : 0;

        return $this->taxCalculator->calculate($amount, $taxRate);
    }
}
