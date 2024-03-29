<?php

namespace Marello\Bundle\CustomerBundle\Provider;

use Doctrine\Common\Util\ClassUtils;
use Marello\Bundle\CustomerBundle\Entity\Customer;

class CustomerAddressProvider
{
    const CACHE_KEY_BILLING = 'billing';
    const CACHE_KEY_SHIPPING = 'shipping';

    /**
     * @var array
     */
    protected $cache = [
        self::CACHE_KEY_BILLING => [],
        self::CACHE_KEY_SHIPPING => [],
    ];

    /**
     * @param Customer|null $customer
     * @return array
     */
    public function getCustomerBillingAddresses(Customer $customer = null)
    {
        $result = [];

        if ($customer) {
            $key = $this->getCacheKey($customer);
            if (array_key_exists($key, $this->cache[self::CACHE_KEY_BILLING])) {
                return $this->cache[self::CACHE_KEY_BILLING][$key];
            }

            $primaryAddress = $customer->getPrimaryAddress();
            if ($primaryAddress) {
                $result[$primaryAddress->getId()] = $primaryAddress;
            }

            foreach ($customer->getAddresses() as $address) {
                $result[$address->getId()] = $address;
            }
            
            $company = $customer->getCompany();
            if ($company) {
                foreach ($company->getAddresses() as $address) {
                    $result[$address->getId()] = $address;
                }
            }
            
            $this->cache[self::CACHE_KEY_BILLING][$key] = $result;

            return $result;
        } else {
            return $this->cache[self::CACHE_KEY_BILLING];
        }
    }

    /**
     * @param Customer|null $customer
     * @return array
     */
    public function getCustomerShippingAddresses(Customer $customer = null)
    {
        $result = [];

        if ($customer) {
            $key = $this->getCacheKey($customer);
            if (array_key_exists($key, $this->cache[self::CACHE_KEY_SHIPPING])) {
                return $this->cache[self::CACHE_KEY_SHIPPING][$key];
            }

            $shippingAddress = $customer->getShippingAddress();
            if ($shippingAddress) {
                $result[$shippingAddress->getId()] = $shippingAddress;
            }

            foreach ($customer->getAddresses() as $address) {
                $result[$address->getId()] = $address;
            }

            $company = $customer->getCompany();
            if ($company) {
                foreach ($company->getAddresses() as $address) {
                    $result[$address->getId()] = $address;
                }
            }
            
            $this->cache[self::CACHE_KEY_SHIPPING][$key] = $result;

            return $result;
        } else {
            return $this->cache[self::CACHE_KEY_SHIPPING];
        }
    }

    /**
     * @param Customer $object
     * @return string
     */
    protected function getCacheKey($object)
    {
        return sprintf(
            '%s_%s',
            ClassUtils::getClass($object),
            $object->getId()
        );
    }
}
