<?php

namespace Marello\Bundle\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Oro\Bundle\AddressBundle\Form\Type\AddressType as OroAddressType;

use Marello\Bundle\AddressBundle\Entity\MarelloAddress;

class AddressType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_address';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('label');
        $builder->remove('organization');

        $builder
            ->add('phone', TextType::class, [
                'required' => false,
            ])
            ->add('company', TextType::class, [
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
            'data_class' => MarelloAddress::class,
            'allow_extra_fields' => true
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return OroAddressType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
