<?php

namespace Marello\Bundle\RefundBundle\Calculator;

use Doctrine\Persistence\ManagerRegistry;
use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Marello\Bundle\RefundBundle\Entity\Refund;
use Marello\Bundle\TaxBundle\Model\ResultElement;
use Marello\Bundle\RefundBundle\Entity\RefundItem;
use Marello\Bundle\TaxBundle\Matcher\TaxRuleMatcherInterface;
use Marello\Bundle\TaxBundle\Calculator\TaxCalculatorInterface;

class RefundBalanceCalculator
{
    /** @var TaxCalculatorInterface $taxCalculator */
    protected $taxCalculator;

    /** @var TaxRuleMatcherInterface $taxRuleMatcher */
    protected $taxRuleMatcher;

    public function __construct(
        protected ManagerRegistry $doctrine
    ) {
    }
    
    /**
     * @param Refund $refund
     * @return float
     */
    public function calculateBalance(Refund $refund)
    {
        $refundsForSameOrder = $this->doctrine
            ->getManagerForClass(Refund::class)
            ->getRepository(Refund::class)
            ->findBy(['order' => $refund->getOrder()]);
        $refundsAmount = $refund->getRefundAmount();
        foreach ($refundsForSameOrder as $prevRefund) {
            if ($refund->getId() !== $prevRefund->getId()) {
                $refundsAmount += $prevRefund->getRefundAmount();
            }
        }

        return $refund->getOrder()->getGrandTotal() - $refundsAmount;
    }

    /**
     * @param Refund $refund
     * @return float
     */
    public function caclulateRefundsTotal(Refund $refund)
    {
        $refundsForSameOrder = $this->doctrine
            ->getManagerForClass(Refund::class)
            ->getRepository(Refund::class)
            ->findBy(['order' => $refund->getOrder()]);
        $refundsAmount = $refund->getRefundAmount();
        foreach ($refundsForSameOrder as $prevRefund) {
            if ($refund->getId() !== $prevRefund->getId()) {
                $refundsAmount += $prevRefund->getRefundAmount();
            }
        }

        return $refundsAmount;
    }

    /**
     * @param Refund $refund
     * @return float
     */
    public function calculateAmount(Refund $refund)
    {
        $sum = array_reduce($refund->getItems()->toArray(), function ($carry, RefundItem $item) {
            return $carry + $item->getRefundAmount();
        }, 0);

        return $sum;
    }

    /**
     * @param array $items
     * $items => [
     *   'quantity' => int itemQuantity,
     *   'taxCode' => int taxCodeId,
     *   'refundAmount' => double refundAmount
     * ]
     * @param Refund $refund
     * @return float[]|int[]
     */
    public function calculateTaxes(array $items, Refund $refund)
    {
        $subtotal = 0;
        $taxTotal = 0;
        $grandTotal = 0;
        foreach ($items as $rowIdentifierKey => $item) {
            if (!empty($item['taxCode'])) {
                $taxTotals = $this->calculateIndividualTaxItem($item, $refund);
                $subtotal += (double)$taxTotals->getExcludingTax();
                $taxTotal += (double)$taxTotals->getTaxAmount();
                $grandTotal += (double)$taxTotals->getIncludingTax();
            }
        }

        return ['subtotal' => $subtotal, 'taxTotal' => $taxTotal, 'grandTotal' => $grandTotal];
    }

    /**
     * @param $item
     * @param $refund
     * @return ResultElement
     */
    public function calculateIndividualTaxItem($item, $refund)
    {
        /** @var TaxCode $taxCode */
        $taxCode = $this->doctrine
            ->getManagerForClass(TaxCode::class)
            ->getRepository(TaxCode::class)
            ->find($item['taxCode']);

        $this->taxRuleMatcher->setOrder($refund->getOrder());
        $taxRule = $this->taxRuleMatcher->match(
            [$taxCode->getCode()],
            $refund->getOrder(),
            $refund->getOrder()->getShippingAddress()
        );
        if ($taxRule) {
            $rate = $taxRule->getTaxRate()->getRate();
        } else {
            $rate = 0;
        }
        $quantity = isset($item['quantity']) ? (double)$item['quantity'] : 1;
        $amount = (double)$item['refundAmount'] * $quantity;
        // set the taxcalculation to always including tax, otherwise it might get confusing from a user's perspective.
        $this->taxCalculator->setIsManualTaxSettingOverride(true);
        return $this->taxCalculator->calculate($amount, $rate);
    }

    /**
     * @param TaxCalculatorInterface $taxCalculator
     */
    public function setTaxCalculator(TaxCalculatorInterface $taxCalculator)
    {
        $this->taxCalculator = $taxCalculator;
    }

    /**
     * @param TaxRuleMatcherInterface $taxRuleMatcher
     */
    public function setTaxRuleMatcher(TaxRuleMatcherInterface $taxRuleMatcher)
    {
        $this->taxRuleMatcher = $taxRuleMatcher;
    }
}
