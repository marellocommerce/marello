<?php

namespace Marello\Bundle\ShippingBundle\Provider\Price;

use Marello\Bundle\ShippingBundle\Context\ShippingContextInterface;
use Marello\Bundle\ShippingBundle\Entity\ShippingMethodConfig;
use Marello\Bundle\ShippingBundle\Entity\ShippingMethodTypeConfig;
use Marello\Bundle\ShippingBundle\Event\ApplicableMethodsEvent;
use Marello\Bundle\ShippingBundle\Method\PricesAwareShippingMethodInterface;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodViewCollection;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodViewFactory;
use Marello\Bundle\ShippingBundle\Provider\Cache\ShippingPriceCache;
use Marello\Bundle\ShippingBundle\Provider\MethodsConfigsRule\Context\MethodsConfigsRulesByContextProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ShippingPriceProvider implements ShippingPriceProviderInterface
{
    /**
     * @var MethodsConfigsRulesByContextProviderInterface
     */
    protected $shippingRulesProvider;

    /**
     * @var ShippingMethodProviderInterface
     */
    protected $shippingMethodProvider;

    /**
     * @var ShippingPriceCache
     */
    protected $priceCache;

    /**
     * @var ShippingMethodViewFactory
     */
    protected $shippingMethodViewFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param MethodsConfigsRulesByContextProviderInterface $shippingRulesProvider
     * @param ShippingMethodProviderInterface              $shippingMethodProvider
     * @param ShippingPriceCache                           $priceCache
     * @param ShippingMethodViewFactory                    $shippingMethodViewFactory
     * @param EventDispatcherInterface                     $eventDispatcher
     */
    public function __construct(
        MethodsConfigsRulesByContextProviderInterface $shippingRulesProvider,
        ShippingMethodProviderInterface $shippingMethodProvider,
        ShippingPriceCache $priceCache,
        ShippingMethodViewFactory $shippingMethodViewFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->shippingRulesProvider = $shippingRulesProvider;
        $this->shippingMethodProvider = $shippingMethodProvider;
        $this->priceCache = $priceCache;
        $this->shippingMethodViewFactory = $shippingMethodViewFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getApplicableMethodsViews(ShippingContextInterface $context)
    {
        $methodCollection = new ShippingMethodViewCollection();

        $rules = $this->shippingRulesProvider->getShippingMethodsConfigsRules($context);
        foreach ($rules as $rule) {
            foreach ($rule->getMethodConfigs() as $methodConfig) {
                $methodId = $methodConfig->getMethod();
                $method = $this->shippingMethodProvider->getShippingMethod($methodId);

                if (!$method) {
                    continue;
                }

                $methodView = $this->shippingMethodViewFactory->createMethodView(
                    $methodId,
                    $method->getLabel(),
                    $method->isGrouped(),
                    $method->getSortOrder()
                );

                $methodCollection->addMethodView($methodId, $methodView);

                $types = $this->getApplicableMethodTypesViews($context, $methodConfig);

                if (count($types) === 0) {
                    continue;
                }

                $methodCollection->addMethodTypesViews($methodId, $types);
            }
        }

        $event = new ApplicableMethodsEvent($methodCollection, $context->getSourceEntity());
        $this->eventDispatcher->dispatch(ApplicableMethodsEvent::NAME, $event);

        return $event->getMethodCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice(ShippingContextInterface $context, $methodId, $typeId)
    {
        $method = $this->shippingMethodProvider->getShippingMethod($methodId);
        if (!$method) {
            return null;
        }

        $type = $method->getType($typeId);
        if (!$type) {
            return null;
        }

        $rules = $this->shippingRulesProvider->getShippingMethodsConfigsRules($context);
        foreach ($rules as $rule) {
            foreach ($rule->getMethodConfigs() as $methodConfig) {
                if ($methodConfig->getMethod() !== $methodId) {
                    continue;
                }

                $typesOptions = $this->getEnabledTypesOptions($methodConfig->getTypeConfigs()->toArray());
                if (array_key_exists($typeId, $typesOptions)) {
                    if ($this->priceCache->hasPrice($context, $methodId, $typeId)) {
                        return $this->priceCache->getPrice($context, $methodId, $typeId);
                    }
                    $price = $type->calculatePrice(
                        $context,
                        $methodConfig->getOptions(),
                        $typesOptions[$typeId]
                    );

                    if ($price) {
                        $this->priceCache->savePrice($context, $methodId, $typeId, $price);
                    }

                    return $price;
                }
            }
        }

        return null;
    }

    /**
     * @param ShippingContextInterface $context
     * @param ShippingMethodConfig $methodConfig
     * @return array
     */
    protected function getApplicableMethodTypesViews(
        ShippingContextInterface $context,
        ShippingMethodConfig $methodConfig
    ) {
        $method = $this->shippingMethodProvider->getShippingMethod($methodConfig->getMethod());
        $methodId = $method->getIdentifier();
        $methodOptions = $methodConfig->getOptions();
        $typesOptions = $this->getEnabledTypesOptions($methodConfig->getTypeConfigs()->toArray());

        $prices = [];
        foreach ($typesOptions as $typeId => $typeOptions) {
            if ($this->priceCache->hasPrice($context, $methodId, $typeId)) {
                $prices[$typeId] = $this->priceCache->getPrice($context, $methodId, $typeId);
            }
        }
        $requestedTypesOptions = array_diff_key($typesOptions, $prices);

        if ($method instanceof PricesAwareShippingMethodInterface && count($requestedTypesOptions) > 0) {
            $prices = array_replace(
                $prices,
                $method->calculatePrices($context, $methodOptions, $requestedTypesOptions)
            );
        } else {
            foreach ($requestedTypesOptions as $typeId => $typeOptions) {
                $type = $method->getType($typeId);
                if ($type) {
                    $prices[$typeId] = $type->calculatePrice($context, $methodOptions, $typeOptions);
                }
            }
        }

        $types = [];
        foreach (array_filter($prices) as $typeId => $price) {
            if (array_key_exists($typeId, $requestedTypesOptions)) {
                $this->priceCache->savePrice($context, $methodId, $typeId, $price);
            }
            $type = $method->getType($typeId);
            $types[$typeId] = $this->shippingMethodViewFactory
                ->createMethodTypeView(
                    $type->getIdentifier(),
                    $type->getLabel(),
                    $type->getSortOrder(),
                    $price
                );
        }

        return $types;
    }

    /**
     * @param array $typeConfigs
     *
     * @return array
     */
    protected function getEnabledTypesOptions(array $typeConfigs)
    {
        return array_reduce(
            $typeConfigs,
            function (array $result, ShippingMethodTypeConfig $config) {
                if ($config->isEnabled()) {
                    $result[$config->getType()] = $config->getOptions();
                }

                return $result;
            },
            []
        );
    }
}
