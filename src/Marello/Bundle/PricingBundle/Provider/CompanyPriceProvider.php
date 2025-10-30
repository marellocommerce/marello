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

        $price = $assembledPriceList->getMsrpPrice();

        $prices[$product->getSku()] = $price instanceof BasePrice ? [$this->roundingService->round($price->getValue())] : [];

        return $prices;
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
            $prices[$company->getCompanyNumber()] = $price;
        }

        return $prices;
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
}
