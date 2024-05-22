<?php

namespace Marello\Bundle\SalesBundle\Form\Type;

use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class SalesChannelCurrencyAwareSelectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('salesChannel', OroEntitySelectOrCreateInlineType::class, [
            'choices' => [],
            'attr' => ['class' => 'dynamic-sales-channel-select']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'currency' => null,
                'autocomplete_alias' => 'currency_sales_channel',
                'create_form_route'  => 'marello_sales_channel_create',
                'grid_name' => 'marello-sales_channels-extended-no-actions-grid',
            ]
        );
    }

    public function getParent()
    {
        return OroEntitySelectOrCreateInlineType::class;
    }
}