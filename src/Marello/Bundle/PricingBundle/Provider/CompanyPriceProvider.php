<?php

namespace Marello\Bundle\PricingBundle\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

use Marello\Bundle\CustomerBundle\Entity\Company;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;

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
    public function getProductPrice($product, $currency, ?Company $company = null): array
    {
        $prices = [];
        /** @var AssembledPriceList $assembledPriceList */
        $assembledPriceList = $this->getAssembledPriceListRepository()->findOneBy(
            ['product' => $product->getId(), 'currency' => $currency]
        );
        if (!$assembledPriceList) {
            return $prices;
        }

        $prices['msrp'] = $this->roundingService->round(
            $assembledPriceList->getMsrpPrice()?->getValue()
        );
        $discountPercent = 0;
        if ($company) {
            $discountPercent = $company->getDiscountPercentage();
        }
        $prices['sales'] = $this->roundingService->round(
            $prices['msrp'] * (((100 - (float)$discountPercent) / 100))
        );

        if ($assembledPriceList->getSpecialPrice()) {
            $prices['special'] = $this->roundingService->round(
                $assembledPriceList->getSpecialPrice()->getValue() * ((100 - (float)$discountPercent) / 100)
            );
            $prices['special_from'] = $assembledPriceList->getSpecialPrice()->getStartDate();
            $prices['special_to'] = $assembledPriceList->getSpecialPrice()->getEndDate();
        }

        return [$prices];
    }

    public function getPricesForCompany(string $currency, Company $company): array
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
