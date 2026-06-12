<?php

namespace Marello\Bundle\PricingBundle\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

use Marello\Bundle\CustomerBundle\Entity\Company;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\PricingBundle\Entity\BasePrice;
use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;

class CompanyPriceProvider implements CompanyPriceProviderInterface
{
    public const PROVIDER_IDENTIFIER = 'basic_price_provider';

    public function __construct(
        protected ManagerRegistry $registry,
        protected AclHelper $aclHelper,
        protected RoundingServiceInterface $roundingService
    ) {
    }

    /**
     * @param $product
     * @param $currency
     * @param $company
     * @return float|null
     * @throws \Oro\Bundle\CurrencyBundle\Exception\InvalidRoundingTypeException
     */
    public function getProductPrice(Product $product, $currency, ?Company $company = null, ?array $parameters = []): array
    {
        $prices[$product->getSku()]['sales'] = number_format(0, $this->roundingService->getPrecision(), '.', '');
        $prices[$product->getSku()]['qty_from'] = 0;
        $prices[$product->getSku()]['qty_to'] = null;

        /** @var AssembledPriceList $assembledPriceList */
        $assembledPriceList = $this->getAssembledPriceListRepository()->findOneBy(
            ['product' => $product->getId(), 'currency' => $currency]
        );
        if (!$assembledPriceList) {
            return [$product->getSku() => $prices];
        }
        $prices[$product->getSku()]['sku'] = $product->getSku();
        $prices[$product->getSku()]['unit'] = $product->getInventoryItem()?->getProductUnit()?->getName();
        $prices[$product->getSku()]['qty_in_unit'] = $product->getInventoryItem()?->getQtyInUnit();
        $prices[$product->getSku()]['msrp'] = number_format(
            $this->roundingService->round($assembledPriceList->getMsrpPrice()?->getValue()),
            $this->roundingService->getPrecision(),
            '.',
            ''
        );

        $discountPercent = 0;
        if ($company) {
            $discountPercent = $company->getDiscountPercentage();
        }

        $prices[$product->getSku()]['sales'] = number_format(
            $this->roundingService->round(
                $prices[$product->getSku()]['msrp'] * (((100 - (float)$discountPercent) / 100))
            ),
            $this->roundingService->getPrecision()
        );

        if ($assembledPriceList->getSpecialPrice()) {
            $prices[$product->getSku()]['special'] = number_format(
                $this->roundingService->round(
                    $assembledPriceList->getSpecialPrice()->getValue() * ((100 - (float)$discountPercent) / 100)
                ),
                $this->roundingService->getPrecision(),
                '.',
                ''
            );
            $prices[$product->getSku()]['special_from'] = $assembledPriceList->getSpecialPrice()->getStartDate();
            $prices[$product->getSku()]['special_to'] = $assembledPriceList->getSpecialPrice()->getEndDate();
        }

        return $prices;
    }

    public function getProductPrices(array $products, string $currency, ?Company $company = null, ?array $parameters = []): array
    {
        $allPrices = [];
        $repo = $this->getRepository(Product::class);
        $productObjects = $repo->findBy(['sku' => $products]);
        foreach ($productObjects as $product) {
            $allPrices[$product->getSku()] = $this->getProductPrice($product, $currency, $company);
        }

        return $allPrices;
    }

    public function getPricesForCompany(string $currency, Company $company, ?array $parameters = []): array
    {
        $prices = [];
        /** @var AssembledPriceList $assembledPriceList */
        $assembledPriceLists = $this->getAssembledPriceListRepository()->findBy(
            ['currency' => $currency]
        );
        if (!$assembledPriceLists) {
            return $prices;
        }

        foreach ($assembledPriceLists as $assembledPriceList) {
            $price = $assembledPriceList->getMsrpPrice();
            $price = $price instanceof BasePrice ? $this->roundingService->round($price->getValue()) : 0;
            $prices[$company->getCompanyNumber()][] = ['price' => $price];
        }

        return [$prices];
    }

    /**
     * @return ObjectRepository
     */
    protected function getAssembledPriceListRepository()
    {
        return $this->getRepository(AssembledPriceList::class);
    }

    /**
     * @param string $className
     * @return ObjectRepository
     */
    protected function getRepository($className)
    {
        return $this->registry->getManagerForClass($className)->getRepository($className);
    }

    /**
     * {@inheritDoc}
     * @return string
     */
    public function getIdentifier(): string
    {
        return self::PROVIDER_IDENTIFIER;
    }

    /**
     * {@inheritDoc}
     * @return string
     */
    public function getLabel(): string
    {
        return 'marello.pricing.provider.label';
    }

    /**
     * {@inheritDoc}
     * @return bool
     */
    public function isEnabled(): bool
    {
        return true;
    }
}
