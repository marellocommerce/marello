<?php

namespace Marello\Bundle\PricingBundle\Provider;

class CompanyPriceProviderRegistry
{
    /**
     * @var CompanyPriceProviderInterface[]
     */
    private $priceProviders = [];

    /**
     * @param CompanyPriceProviderInterface $provider
     * @return $this
     */
    public function addPriceProvider(CompanyPriceProviderInterface $provider): self
    {
        $this->priceProviders[$provider->getIdentifier()] = $provider;

        return $this;
    }

    /**
     * @param string $identifier
     * @return null|CompanyPriceProviderInterface
     */
    public function getPriceProvider(string $identifier):? CompanyPriceProviderInterface
    {
        if ($this->hasPriceProvider($identifier)) {
            return $this->priceProviders[$identifier];
        }
        return null;
    }

    /**
     * @return CompanyPriceProviderInterface[]
     */
    public function getPriceProviders(): array
    {
        return $this->priceProviders;
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function hasPriceProvider(string $identifier): bool
    {
        if (array_key_exists($identifier, $this->priceProviders)) {
            return true;
        }
        return false;
    }
}
