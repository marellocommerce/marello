<?php

namespace Marello\Bundle\TaxBundle\Matcher;

use Oro\Bundle\AddressBundle\Entity\AbstractAddress as BaseAbstractAddress;

use Marello\Bundle\TaxBundle\Entity\TaxRule;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\AddressBundle\Entity\AbstractAddress;

interface TaxRuleMatcherInterface
{
    /**
     * @param Order|null $order
     * @param BaseAbstractAddress|AbstractAddress|null $address
     * @param array $taxCodes
     * @return TaxRule
     */
    public function match(array $taxCodes, Order $order = null, BaseAbstractAddress|AbstractAddress $address = null);
}
