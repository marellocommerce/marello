<?php
namespace Marello\Bundle\ShippingBundle\Provider\Price;

use Marello\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;

class EnabledMethodsShippingPriceProviderDecorator implements ShippingPriceProviderInterface
{
    /**
     * @var ShippingPriceProviderInterface
     */
    protected $provider;

    /**
     * @var ShippingMethodProviderInterface
     */
    protected $shippingMethodProvider;

    /**
     * @param ShippingPriceProviderInterface  $provider
     * @param ShippingMethodProviderInterface $shippingMethodProvider
     */
    public function __construct(
        ShippingPriceProviderInterface $provider,
        ShippingMethodProviderInterface $shippingMethodProvider
    ) {
        $this->provider = $provider;
        $this->shippingMethodProvider = $shippingMethodProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getApplicableMethodsViews(ShippingContextInterface $context)
    {
        $methodViewCollection = clone $this->provider->getApplicableMethodsViews($context);
        $methodViews = $methodViewCollection->getAllMethodsViews();
        foreach ($methodViews as $methodId => $methodView) {
            $method = $this->shippingMethodProvider->getShippingMethod($methodId);
            if (!$method->isEnabled()) {
                $methodViewCollection->removeMethodView($methodId);
            }
        }

        return $methodViewCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(ShippingContextInterface $context, $methodId, $typeId)
    {
        return $this->provider->getPrice($context, $methodId, $typeId);
    }
}
