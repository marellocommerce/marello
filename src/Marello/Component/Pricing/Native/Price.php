<?php

namespace Marello\Component\Pricing\Native;

use Marello\Component\Pricing\OverflowException;
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
     * @param int $amount
     * @param int $precision
     * @param string $currency
     */
    public function __construct($amount, $precision, $currency)
    {
        if ($amount > PHP_INT_MAX || $amount < PHP_INT_MIN) {
            throw new OverflowException(sprintf('%f is too large to be stored as a currency amount.'));
        }

        $this->amount = $amount;
        $this->precision = $precision;
        $this->currency = $currency;
    }

    /**
     * @return int
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