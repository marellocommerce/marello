<?php

namespace Marello\Bundle\TaxBundle\Matcher;

use Marello\Bundle\TaxBundle\Provider\CompanyReverseTaxProvider;
use Oro\Bundle\AddressBundle\Entity\AbstractAddress;

use Marello\Bundle\OrderBundle\Entity\Order;

class CompositeTaxRuleMatcher implements TaxRuleMatcherInterface
{
    const CACHE_KEY_DELIMITER = ':';

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @var TaxRuleMatcherInterface[]
     */
    private $matchers = [];

    /** @var CompanyReverseTaxProvider $provider */
    protected $provider;

    /** @var Order $order */
    protected $order;

    /**
     * @param TaxRuleMatcherInterface $matcher
     */
    public function addMatcher(TaxRuleMatcherInterface $matcher)
    {
        $this->matchers[] = $matcher;
    }

    /**
     * {@inheritdoc}
     */
    public function match(AbstractAddress $address = null, array $taxCodes)
    {
        if (null === $address || null === $address->getCountry() || 0 === count($taxCodes)) {
            return null;
        }

        if (!$this->provider->orderIsTaxable($this->order)) {
            return null;
        }

        $cacheKey = $this->getCacheKey($address, $taxCodes);
        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }
        foreach ($this->matchers as $matcher) {
            $matcher->setOrder($this->order);
            $taxRule = $matcher->match($address, $taxCodes);
            if ($taxRule) {
                $this->cache[$cacheKey] = $taxRule;

                return $this->cache[$cacheKey];
            }
        }
        
        return null;
    }

    /**
     * @param AbstractAddress $address
     * @param array $taxCodes
     * @return string
     */
    protected function getCacheKey(AbstractAddress $address, array $taxCodes)
    {
        $countryCode = $address->getCountryIso2();
        $regionCode = $address->getRegionCode() ? : $address->getRegionText();
        $zipCode = $address->getPostalCode();
        $taxCodesHash = md5(json_encode($taxCodes));

        return sprintf('%s:%s:%s:%s', $countryCode, $regionCode, $zipCode, $taxCodesHash);
    }

    /**
     * @param Order|null $order
     * @return void
     */
    public function setOrder(Order $order = null)
    {
        $this->order = $order;
    }

    /**
     * @param CompanyReverseTaxProvider $provider
     * @return void
     */
    public function setCompanyReverseTaxProvider(CompanyReverseTaxProvider $provider): void
    {
        $this->provider = $provider;
    }
}
