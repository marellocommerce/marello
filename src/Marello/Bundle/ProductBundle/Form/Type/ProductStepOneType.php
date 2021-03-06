<?php

namespace Marello\Bundle\ProductBundle\Form\Type;

use Marello\Bundle\ProductBundle\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductStepOneType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_product_step_one';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_token_id'        => 'product',
            'data_class'           => Product::class,
            'validation_groups'    => ['product_create_step_one'],
            'enable_attribute_family' => true,
            'ownership_disabled' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type', ProductTypeSelectType::class, ['label' => 'marello.product.type.label']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['default_input_action'] = 'marello_product_form';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
