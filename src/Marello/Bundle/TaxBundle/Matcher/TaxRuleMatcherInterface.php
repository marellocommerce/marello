<?php

namespace Marello\Bundle\TaxBundle\Matcher;

use Oro\Bundle\AddressBundle\Entity\AbstractAddress;

use Marello\Bundle\TaxBundle\Entity\TaxRule;
use Marello\Bundle\OrderBundle\Entity\Order;

interface TaxRuleMatcherInterface
{
    /**
     * @param Order|null $order
     * @param AbstractAddress|null $address
     * @param array $taxCodes
     * @return TaxRule
     */
    public function match(AbstractAddress $address = null, array $taxCodes);
}
