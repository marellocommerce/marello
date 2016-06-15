<?php

namespace Marello\Component\Pricing;

interface PriceCalculatorInterface
{
    /**
     * @param PriceInterface $lhs
     * @param PriceInterface[] ...$operands
     * @return PriceInterface
     */
    public function add(PriceInterface $lhs, PriceInterface ...$operands);

    /**
     * @param PriceInterface $lhs
     * @param PriceInterface[] ...$operands
     * @return PriceInterface
     */
    public function subtract(PriceInterface $lhs, PriceInterface ...$operands);

    /**
     * @param PriceInterface $operand
     * @param int $factor
     * @return PriceInterface
     */
    public function multiply(PriceInterface $operand, $factor);

    /**
     * @param PriceInterface $operand
     * @param int $factor
     * @return PriceInterface
     */
    public function divide(PriceInterface $operand, $factor);
}