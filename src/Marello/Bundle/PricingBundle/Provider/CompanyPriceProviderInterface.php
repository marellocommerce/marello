<?php

namespace Marello\Bundle\PricingBundle\Provider;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\CustomerBundle\Entity\Company;

interface CompanyPriceProviderInterface
{
    /**
     * @param Product $product
     * @param string $currency
     * @param Company|null $company
     * @return array
     */
    public function getProductPrice(Product $product, string $currency, ?Company $company = null, ?array $parameters = []): array;

    /**
     * @param array $products array of product SKU's
     * @param string $currency
     * @param Company|null $company
     * @return array
     */
    public function getProductPrices(array $products, string $currency, ?Company $company = null, ?array $parameters = []): array;

    /**
     * @param string $currency
     * @param Company $company
     * @return array
     */
    public function getPricesForCompany(string $currency, Company $company, ?array $parameters = []): array;

    /**
     * Identifier for the price proivder
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * Label for the price provider
     * @return string
     */
    public function getLabel(): string;

    /**
     * Check if provider is enabled
     * @return bool
     */
    public function isEnabled(): bool;
}
