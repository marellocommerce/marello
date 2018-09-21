<?php

namespace Marello\Bundle\ShippingBundle\Checker;

use Marello\Bundle\ShippingBundle\Entity\ShippingMethodsConfigsRule;

class ShippingRuleEnabledChecker implements ShippingRuleEnabledCheckerInterface
{
    /**
     * @var ShippingMethodEnabledByIdentifierCheckerInterface
     */
    private $methodEnabledChecker;

    /**
     * @param ShippingMethodEnabledByIdentifierCheckerInterface $methodEnabledChecker
     */
    public function __construct(ShippingMethodEnabledByIdentifierCheckerInterface $methodEnabledChecker)
    {
        $this->methodEnabledChecker = $methodEnabledChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeEnabled(ShippingMethodsConfigsRule $rule)
    {
        foreach ($rule->getMethodConfigs() as $config) {
            if ($this->methodEnabledChecker->isEnabled($config->getMethod())) {
                return true;
            }
        }

        return false;
    }
}
