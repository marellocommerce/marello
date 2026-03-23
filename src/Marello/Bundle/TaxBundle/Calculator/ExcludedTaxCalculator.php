<?php

namespace Marello\Bundle\TaxBundle\Calculator;

use Marello\Bundle\TaxBundle\Model\ResultElement;
use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;

class ExcludedTaxCalculator implements TaxCalculatorInterface
{
    /**
     * @var RoundingServiceInterface
     */
    protected $rounding;

    /**
     * @param RoundingServiceInterface $rounding
     */
    public function __construct(RoundingServiceInterface $rounding)
    {
        $this->rounding = $rounding;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate($amount, $taxRate)
    {
        $exclTax = (double)$amount;
        $taxRate = abs($taxRate);

        $inclTax = $exclTax * (1 + $taxRate);
        $taxAmount = $inclTax - $exclTax;
        $roundingPrecision = $this->rounding->getPrecision();

        return ResultElement::create(
            number_format($this->rounding->round($inclTax), $roundingPrecision),
            number_format($this->rounding->round($exclTax), $roundingPrecision),
            number_format($this->rounding->round($taxAmount), $roundingPrecision)
        );
    }
}
