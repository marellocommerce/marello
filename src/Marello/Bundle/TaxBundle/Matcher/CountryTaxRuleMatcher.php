<?php

namespace Marello\Bundle\TaxBundle\Matcher;

use Oro\Bundle\AddressBundle\Entity\AbstractAddress;

use Marello\Bundle\OrderBundle\Entity\Order;

class CountryTaxRuleMatcher extends AbstractTaxRuleMatcher
{
    /**
     * {@inheritdoc}
     */
    public function match(array $taxCodes, Order $order = null, AbstractAddress $address = null)
    {
        if (null === $address) {
            return null;
        }
        $country = $address->getCountry();
        if (null === $country || 0 === count($taxCodes)) {
            return null;
        }
        $taxRules = $this->getTaxRuleRepository()->findByCountryAndTaxCode($taxCodes, $country);

        return !empty($taxRules) ? reset($taxRules) : null;
    }
}
