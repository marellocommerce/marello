<?php

namespace Marello\Bundle\ShippingBundle\Provider;

use Marello\Bundle\ShippingBundle\Method\ShippingMethodInterface;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

class BasicShippingMethodChoicesProvider implements ShippingMethodChoicesProviderInterface
{
    /**
     * @var ShippingMethodProviderInterface
     */
    protected $shippingMethodProvider;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param ShippingMethodProviderInterface $shippingMethodProvider
     * @param TranslatorInterface             $translator
     */
    public function __construct(
        ShippingMethodProviderInterface $shippingMethodProvider,
        TranslatorInterface $translator
    ) {
        $this->shippingMethodProvider = $shippingMethodProvider;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethods($translate = false)
    {
        return array_reduce(
            $this->shippingMethodProvider->getShippingMethods(),
            function (array $result, ShippingMethodInterface $method) use ($translate) {
                $label = $method->getLabel();
                if ($translate) {
                    $label = $this->translator->trans($label);
                }
                $result[$method->getIdentifier()] = $label;

                return $result;
            },
            []
        );
    }
}
