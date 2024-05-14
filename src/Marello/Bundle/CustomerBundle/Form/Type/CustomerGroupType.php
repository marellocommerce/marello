<?php

namespace Marello\Bundle\CustomerBundle\Form\Type;

use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\CustomerBundle\Entity\CustomerGroup;
use Oro\Bundle\FormBundle\Form\Type\EntityIdentifierType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomerGroupType extends AbstractType
{
    const NAME = 'marello_customer_group';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'marello.customer_group.name.label'])
            ->add(
                'appendCustomers',
                EntityIdentifierType::class,
                [
                    'class'    => Customer::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            )
            ->add(
                'removeCustomers',
                EntityIdentifierType::class,
                [
                    'class'    => Customer::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomerGroup::class,
            'intention' => 'company',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::NAME;
    }
}