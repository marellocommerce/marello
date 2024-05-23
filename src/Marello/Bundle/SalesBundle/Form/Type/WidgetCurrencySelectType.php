<?php

namespace Marello\Bundle\SalesBundle\Form\Type;

use Oro\Bundle\CurrencyBundle\Form\Type\CurrencyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WidgetCurrencySelectType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_sales_widget_currency_select';

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
                'template' => '@MarelloSales/SalesChannel/widget/currencySelect.html.twig',
                'component' => 'currency-select-component',
            ],
        ]);
    }
}