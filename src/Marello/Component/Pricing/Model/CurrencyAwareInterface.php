<?php

namespace Marello\Component\Pricing\Model;

interface CurrencyAwareInterface
{
    /**
     * @return string
     */
    public function getCurrency();
}
