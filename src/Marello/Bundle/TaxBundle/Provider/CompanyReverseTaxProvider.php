<?php

namespace Marello\Bundle\TaxBundle\Provider;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\TaxBundle\DependencyInjection\Configuration;

class CompanyReverseTaxProvider
{
    /**
     * @param ConfigManager $configManager
     */
    public function __construct(
        protected ConfigManager $configManager
    ) {
    }

    /**
     * @param Order|null $order
     * @return bool
     */
    public function orderIsTaxable(Order $order = null)
    {
        // the order has a company attached
        // the company has a 'valid' tax identification number
        // the origin shipping country is not the same as the shipping address country from the order.
        if ($order && $order->getCustomer()) {
            if ($company = $order->getCustomer()->getCompany()) {
                if ($shippingAddress = $order->getShippingAddress()) {
                    $originCountry = $this->configManager->get(Configuration::SYSTEM_TAX_ORIGIN_COUNTRY_CONFIG_PATH);
                    if ($company->getTaxIdentificationNumber() &&
                        $originCountry->getIso2Code() !== $shippingAddress->getCountryIso2()
                    ) {
                        return false;
                    }
                }
            }
        }

        return true;
    }
}
