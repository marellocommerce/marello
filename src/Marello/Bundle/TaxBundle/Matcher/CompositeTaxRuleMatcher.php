<?php

namespace Marello\Bundle\TaxBundle\Matcher;

use Oro\Bundle\AddressBundle\Entity\AbstractAddress;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\TaxBundle\Provider\CompanyReverseTaxProvider;

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

    public function __construct(
        protected CompanyReverseTaxProvider $provider
    ) {
    }

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
    public function match(array $taxCodes, Order $order = null, AbstractAddress $address = null)
    {
        if (null === $address || null === $address->getCountry() || 0 === count($taxCodes)) {
            return null;
        }

        if (!$this->provider->orderIsTaxable($order)) {
            return null;
        }

        $cacheKey = $this->getCacheKey($address, $taxCodes);
        if (array_key_exists($cacheKey, $this->cache)) {
            return $this->cache[$cacheKey];
        }
        foreach ($this->matchers as $matcher) {
            $taxRule = $matcher->match($taxCodes, $order, $address);
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
}
