<?php

namespace Marello\Component\Pricing\Native;

use Marello\Component\Pricing\IncompatibleCurrencyException;
use Marello\Component\Pricing\PriceCalculatorInterface;
use Marello\Component\Pricing\PriceInterface;

class PriceCalculator implements PriceCalculatorInterface
{
    /**
     * @param PriceInterface $price
     * @param int $precision
     * @return int
     */
    private function toPrecision(PriceInterface $price, $precision = 1)
    {
        if ($price->getPrecision() === $precision) {
            return $price->getAmount();
        }

        $multiplier = pow(10, $precision);
        return (int) ($price->getAmount() * $multiplier);
    }

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
            $result += $this->toPrecision($operand);
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
            $result -= $this->toPrecision($operand);
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
        return new Price(round($operand->getAmount() * $factor), $operand->getPrecision(), $operand->getCurrency());
    }

    /**
     * @param PriceInterface $operand
     * @param int $factor
     * @return PriceInterface
     */
    public function divide(PriceInterface $operand, $factor)
    {
        return new Price(round($operand->getAmount() / $factor), $operand->getPrecision(), $operand->getCurrency());
    }
}