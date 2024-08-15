<?php

namespace Marello\Bundle\TaxBundle\Calculator;

use Marello\Bundle\PricingBundle\DependencyInjection\Configuration;
use Marello\Bundle\TaxBundle\Model\ResultElement;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;

class TaxCalculator implements TaxCalculatorInterface
{
    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var TaxCalculatorInterface
     */
    protected $includedTaxCalculator;

    /**
     * @var TaxCalculatorInterface
     */
    protected $excludedTaxCalculator;

    /** @var bool */
    private $manualTaxOverride;

    /**
     * @param ConfigManager $configManager
     * @param TaxCalculatorInterface $includedTaxCalculator
     * @param TaxCalculatorInterface $excludedTaxCalculator
     */
    public function __construct(
        ConfigManager $configManager,
        TaxCalculatorInterface $includedTaxCalculator,
        TaxCalculatorInterface $excludedTaxCalculator
    ) {
        $this->configManager = $configManager;
        $this->includedTaxCalculator = $includedTaxCalculator;
        $this->excludedTaxCalculator = $excludedTaxCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate($amount, $taxRate): ResultElement
    {
        if ($this->isPricesIncludeTax() || $this->getIsManualTaxSettingOverride()) {
            return $this->includedTaxCalculator->calculate($amount, $taxRate);
        }

        return $this->excludedTaxCalculator->calculate($amount, $taxRate);
    }
    
    protected function isPricesIncludeTax()
    {
        return $this->configManager->get(Configuration::VAT_SYSTEM_CONFIG_PATH);
    }

    public function setIsManualTaxSettingOverride(bool $manualOverride = false): self
    {
        $this->manualTaxOverride = $manualOverride;

        return $this;
    }

    private function getIsManualTaxSettingOverride(): bool
    {
        return $this->manualTaxOverride;
    }
}
