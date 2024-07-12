<?php

namespace Marello\Bundle\SalesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalesChannelCurrencyAwareSelectType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_sales_channel_currency_aware_select';

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        if ($options['configs']['component'] != 'currency-aware') {
            $options['configs']['component'] .= '-currency-aware';
        };
        $options['configs']['extra_config'] = 'currency_aware';
        $view->vars = array_replace_recursive($view->vars, ['configs' => $options['configs']]);
    }

    public function getParent()
    {
        return SalesChannelSelectType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
