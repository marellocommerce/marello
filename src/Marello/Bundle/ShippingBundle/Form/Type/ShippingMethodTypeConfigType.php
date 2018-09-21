<?php

namespace Marello\Bundle\ShippingBundle\Form\Type;

use Marello\Bundle\ShippingBundle\Entity\ShippingMethodTypeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraint;

class ShippingMethodTypeConfigType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_shipping_method_type_config';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['is_grouped']) {
            $builder->add('enabled', CheckboxType::class);
        } else {
            $builder->add('enabled', HiddenType::class, [
                'data' => true,
            ]);
        }
        $builder->add('type', HiddenType::class);
        $builder->add('options', $options['options_type']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShippingMethodTypeConfig::class,
            'options_type' => HiddenType::class,
            'is_grouped' => false,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if ($data && $data->isEnabled()) {
                    return [Constraint::DEFAULT_GROUP];
                }
                return [];
            },
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
