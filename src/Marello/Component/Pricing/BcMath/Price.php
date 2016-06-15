<?php

namespace Marello\Component\Pricing\BcMath;

use Marello\Component\Pricing\PriceInterface;

class Price implements PriceInterface
{
    /**
     * @var int
     */
    private $amount;

    /**
     * @var int
     */
    private $precision;

    /**
     * @var string
     */
    private $currency;

    /**
     * Price constructor.
     * @param string $amount
     * @param int $precision
     * @param string $currency
     */
    public function __construct($amount, $precision, $currency)
    {
        $this->amount = $amount;
        $this->precision = $precision;
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function toFloat()
    {
        return (float) $this->amount / $this->precision;
    }
}