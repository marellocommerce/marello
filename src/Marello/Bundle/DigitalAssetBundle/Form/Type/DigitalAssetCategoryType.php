<?php

namespace Marello\Bundle\DigitalAssetBundle\Form\Type;

use Marello\Bundle\DigitalAssetBundle\Entity\DigitalAssetCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DigitalAssetCategoryType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_digital_asset_category';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [ 'label' => 'marello.digitalasset.digitalassetcategory.name.label', 'required' => true ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DigitalAssetCategory::class
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