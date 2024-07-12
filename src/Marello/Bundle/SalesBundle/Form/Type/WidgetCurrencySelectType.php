<?php

namespace Marello\Bundle\SalesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\LocaleBundle\Model\LocaleSettings;
use Oro\Bundle\CurrencyBundle\Form\Type\CurrencyType;

class WidgetCurrencySelectType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_sales_widget_currency_select';

    public function __construct(
        protected LocaleSettings $localeSettings
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CurrencyType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'configs'            => [
                'component' => 'currency-select-component',
            ],
            'data' => $this->localeSettings->getCurrency()
        ]);
    }
}
