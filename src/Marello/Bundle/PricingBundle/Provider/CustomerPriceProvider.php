<?php

namespace Marello\Bundle\PricingBundle\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Oro\Bundle\CurrencyBundle\Rounding\RoundingServiceInterface;

use Marello\Bundle\PricingBundle\Entity\BasePrice;
use Marello\Bundle\PricingBundle\Entity\AssembledPriceList;

class CustomerPriceProvider implements CustomerPriceProviderInterface
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
     * @param $customer
     * @return float|null
     * @throws \Oro\Bundle\CurrencyBundle\Exception\InvalidRoundingTypeException
     */
    public function getPriceForCustomer($product, $currency, $customer = null): ?float
    {
        /** @var AssembledPriceList $assembledPriceList */
        $assembledPriceList = $this->getAssembledPriceListRepository()->findOneBy(
            ['product' => $product->getId(), 'currency' => $currency]
        );
        if (!$assembledPriceList) {
            return null;
        }

        $price = $assembledPriceList->getMsrpPrice();

        return $price instanceof BasePrice ? $this->roundingService->round($price->getValue()) : null;
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
