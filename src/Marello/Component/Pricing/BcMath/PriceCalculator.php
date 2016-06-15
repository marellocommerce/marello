<?php

namespace Marello\Component\Pricing\BcMath;

use Marello\Component\Pricing\IncompatibleCurrencyException;
use Marello\Component\Pricing\PriceCalculatorInterface;
use Marello\Component\Pricing\PriceInterface;

class PriceCalculator implements PriceCalculatorInterface
{
    /**
     * @param PriceInterface $lhs
     * @param PriceInterface[] ...$operands
     * @return PriceInterface
     * @throws IncompatibleCurrencyException
     */
    public function add(PriceInterface $lhs, PriceInterface ...$operands)
    {
        $result = $lhs->getAmount();
        /** @var PriceInterface $operand */
        foreach ($operands as $operand) {
            if ($operand->getCurrency() !== $lhs->getCurrency()) {
                throw new IncompatibleCurrencyException(sprintf(
                    'Could not subtract %s with %s', $lhs->getCurrency(), $operand->getCurrency()));
            }
            bcadd($result, $operand->getAmount(), $lhs->getPrecision());
        }

        return new Price($result, $lhs->getPrecision(), $lhs->getCurrency());
    }

    /**
     * @param PriceInterface $lhs
     * @param \Marello\Component\Pricing\PriceInterface[] ...$operands
     * @return PriceInterface
     * @throws IncompatibleCurrencyException
     */
    public function subtract(PriceInterface $lhs, PriceInterface ...$operands)
    {
        $result = $lhs->getAmount();
        /** @var PriceInterface $operand */
        foreach ($operands as $operand) {
            if ($operand->getCurrency() !== $lhs->getCurrency()) {
                throw new IncompatibleCurrencyException(sprintf(
                    'Could not subtract %s with %s', $lhs->getCurrency(), $operand->getCurrency()));
            }
            bcsub($result, $operand->getAmount(), $lhs->getPrecision());
        }

        return new Price($result, $lhs->getPrecision(), $lhs->getCurrency());
    }

    /**
     * @param PriceInterface $operand
     * @param int $factor
     * @return PriceInterface
     */
    public function multiply(PriceInterface $operand, $factor)
    {
        return new Price(bcmul($operand->getAmount(), $factor, $operand->getPrecision()),
            $operand->getPrecision(), $operand->getCurrency());
    }

    /**
     * @param PriceInterface $operand
     * @param int $factor
     * @return PriceInterface
     */
    public function divide(PriceInterface $operand, $factor)
    {
        return new Price(bcdiv($operand->getAmount(), $factor, $operand->getPrecision()),
            $operand->getPrecision(), $operand->getCurrency());
    }
}