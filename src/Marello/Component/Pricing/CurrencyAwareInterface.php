<?php

namespace Marello\Component\Pricing;

interface CurrencyAwareInterface
{
    /**
     * @return string
     */
    public function getCurrency();
}
