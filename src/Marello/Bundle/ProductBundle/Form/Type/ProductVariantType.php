<?php

namespace Marello\Bundle\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Oro\Bundle\AttachmentBundle\Form\Type\FileType;
use Oro\Bundle\AttachmentBundle\Form\Type\ImageType;
use Oro\Bundle\FormBundle\Form\Type\OroRichTextType;
use Oro\Bundle\FormBundle\Form\Type\EntityIdentifierType;
use Oro\Bundle\FormBundle\Form\Extension\StripTagsExtension;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\Variant;
use Marello\Bundle\ProductBundle\Form\EventListener\VariantSubscriber;

class ProductVariantType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_product_variant_form';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('variantCode', TextType::class)
            ->add(
                'names',
                LocalizedFallbackValueCollectionType::class,
                [
                    'label' => 'marello.product.variant.name.label',
                    'required' => false,
                    'entry_options' => [
                        StripTagsExtension::OPTION_NAME => true
                    ]
                ]
            )
            ->add(
                'descriptions',
                LocalizedFallbackValueCollectionType::class,
                [
                    'label' => 'marello.product.variant.description.label',
                    'required' => false,
                    'field' => 'text',
                    'entry_type' => OroRichTextType::class,
                    'entry_options' => [
                        'wysiwyg_options' => [
                            'elementpath' => true,
                            'resize' => true,
                            'height' => 300
                        ]
                    ],
                    'use_tabs' => true
                ]
            )
            ->add(
                'image',
                ImageType::class,
                [
                    'label' => 'marello.product.variant.image.label',
                    'required' => false
                ]
            )
            ->add(
                'ARFile',
                FileType::class,
                [
                    'label' => 'marello.product.variant.a_r_file.label',
                    'required' => false
                ]
            )
            ->add(
                'addVariants',
                EntityIdentifierType::class,
                [
                    'class'    => Product::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            )
            ->add(
                'removeVariants',
                EntityIdentifierType::class,
                [
                    'class'    => Product::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            );

        $builder->addEventSubscriber(new VariantSubscriber());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Variant::class,
            'intention'          => 'variant',
            'single_form'        => false,
            'constraints'        => [new Valid()],
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
