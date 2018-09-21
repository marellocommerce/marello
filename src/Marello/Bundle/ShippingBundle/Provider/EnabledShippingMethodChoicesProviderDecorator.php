<?php

namespace Marello\Bundle\ShippingBundle\Provider;

use Marello\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;

class EnabledShippingMethodChoicesProviderDecorator implements ShippingMethodChoicesProviderInterface
{
    /**
     * @var ShippingMethodProviderInterface
     */
    protected $shippingMethodProvider;

    /**
     * @var ShippingMethodChoicesProviderInterface
     */
    protected $provider;

    /**
     * @param ShippingMethodProviderInterface        $shippingMethodProvider
     * @param ShippingMethodChoicesProviderInterface $provider
     */
    public function __construct(
        ShippingMethodProviderInterface $shippingMethodProvider,
        ShippingMethodChoicesProviderInterface $provider
    ) {
        $this->shippingMethodProvider = $shippingMethodProvider;
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethods($translate = false)
    {
        $methods = $this->provider->getMethods($translate);
        $enabledMethods = [];
        foreach ($methods as $methodId => $label) {
            $method = $this->shippingMethodProvider->getShippingMethod($methodId);
            if ($method->isEnabled()) {
                $enabledMethods[$methodId] = $label;
            }
        }

        return $enabledMethods;
    }
}
