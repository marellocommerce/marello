<?php

namespace Marello\Bundle\PricingBundle\Provider;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\CustomerBundle\Entity\Customer;

interface CustomerPriceProviderInterface
{
    /**
     * @param Product $product
     * @param string $currency
     * @param ?Customer $customer
     * @return float|null
     */
    public function getPriceForCustomer($product, $currency, ?Customer $customer): ?float;
}
