<?php

namespace Marello\Bundle\ShippingBundle\Twig;

use Marello\Bundle\ShippingBundle\Checker\ShippingMethodEnabledByIdentifierCheckerInterface;
use Marello\Bundle\ShippingBundle\Event\ShippingMethodConfigDataEvent;
use Marello\Bundle\ShippingBundle\Formatter\ShippingMethodLabelFormatter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ShippingMethodExtension extends AbstractExtension
{
    const SHIPPING_METHOD_EXTENSION_NAME = 'marello_shipping_method';
    const DEFAULT_METHOD_CONFIG_TEMPLATE
        = '@MarelloShipping/ShippingMethodsConfigsRule/shippingMethodWithOptions.html.twig';

    /**
     * @var ShippingMethodLabelFormatter
     */
    protected $shippingMethodLabelFormatter;

    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var ShippingMethodEnabledByIdentifierCheckerInterface
     */
    protected $checker;

    /**
     * @var array
     */
    protected $configCache = [];

    /**
     * @param ShippingMethodLabelFormatter                      $shippingMethodLabelFormatter
     * @param EventDispatcherInterface                          $dispatcher
     * @param ShippingMethodEnabledByIdentifierCheckerInterface $checker
     */
    public function __construct(
        ShippingMethodLabelFormatter $shippingMethodLabelFormatter,
        EventDispatcherInterface $dispatcher,
        ShippingMethodEnabledByIdentifierCheckerInterface $checker
    ) {
        $this->shippingMethodLabelFormatter = $shippingMethodLabelFormatter;
        $this->dispatcher = $dispatcher;
        $this->checker = $checker;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return static::SHIPPING_METHOD_EXTENSION_NAME;
    }

    /**
     * @param string $shippingMethodName
     *
     * @return string Shipping Method config template path
     */
    public function getShippingMethodConfigRenderData($shippingMethodName)
    {
        $event = new ShippingMethodConfigDataEvent($shippingMethodName);
        if (!array_key_exists($shippingMethodName, $this->configCache)) {
            $this->dispatcher->dispatch($event, ShippingMethodConfigDataEvent::NAME);
            $template = $event->getTemplate();
            if (!$template) {
                $template = static::DEFAULT_METHOD_CONFIG_TEMPLATE;
            }
            $this->configCache[$shippingMethodName] = $template;
        }

        return $this->configCache[$shippingMethodName];
    }

    /**
     * @param string $methodIdentifier
     *
     * @return bool
     */
    public function isShippingMethodEnabled($methodIdentifier)
    {
        return $this->checker->isEnabled($methodIdentifier);
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'marello_get_shipping_method_label',
                [$this->shippingMethodLabelFormatter, 'formatShippingMethodLabel']
            ),
            new TwigFunction(
                'marello_get_shipping_method_type_label',
                [$this->shippingMethodLabelFormatter, 'formatShippingMethodTypeLabel']
            ),
            new TwigFunction(
                'marello_shipping_method_with_type_label',
                [$this->shippingMethodLabelFormatter, 'formatShippingMethodWithTypeLabel']
            ),
            new TwigFunction(
                'marello_shipping_method_config_template',
                [$this, 'getShippingMethodConfigRenderData']
            ),
            new TwigFunction(
                'marello_shipping_method_enabled',
                [$this, 'isShippingMethodEnabled']
            )
        ];
    }
}
