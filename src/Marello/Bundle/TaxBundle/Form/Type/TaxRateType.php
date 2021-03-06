<?php

namespace Marello\Bundle\TaxBundle\Form\Type;

use Marello\Bundle\TaxBundle\Entity\TaxRate;
use Oro\Bundle\FormBundle\Form\Type\OroPercentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

class TaxRateType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_tax_rate_form';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'code',
                TextType::class,
                [
                    'required' => true,
                    'constraints' => new NotNull()
                ]
            )
            ->add(
                'rate',
                OroPercentType::class,
                [
                    'label' => 'marello.tax.taxrate.rate.label',
                    'required' => true,
                    'constraints' => new NotNull()
                ]
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'  => TaxRate::class,
            'constraints' => [new Valid()],
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
