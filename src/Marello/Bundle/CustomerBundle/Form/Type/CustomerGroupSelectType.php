<?php

namespace Marello\Bundle\CustomerBundle\Form\Type;

use Marello\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerGroupSelectType extends AbstractType
{
    const NAME = 'marello_customer_group_select';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'marello_customer_group',
                'grid_name' => 'marello-customer-group-select-grid',
                'entity_class'          => CustomerGroup::class,
                'create_enabled'        => true,
                'create_form_route' => 'marello_customer_group_create',
                'configs' => [
                    'placeholder' => 'marello.customer.customergroup.form.choose',
                ],
                'attr' => [
                    'class' => 'marello-customer-group-select',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return OroEntitySelectOrCreateInlineType::class;
    }
}
