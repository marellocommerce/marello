<?php

namespace Marello\Bundle\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\FormBundle\Form\Type\CheckboxType;
use Oro\Bundle\TranslationBundle\Form\Type\TranslatableEntityType;
use Oro\Bundle\AddressBundle\Entity\AddressType as BaseEntityAddressType;

use Marello\Bundle\AddressBundle\Entity\MarelloTypedAddress;

class TypedAddressType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_typed_address';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'addressType',
                TranslatableEntityType::class,
                array(
                    'class' => BaseEntityAddressType::class,
                    'choice_label' => 'label',
                    'required' => false,
                    'multiple' => false,
                    'expanded' => false
                ))
            ->add('isDefault', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MarelloTypedAddress::class,
            'allow_extra_fields' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return AddressType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
