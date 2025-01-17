<?php

namespace Marello\Bundle\InventoryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Oro\Bundle\FormBundle\Form\Type\OroRichTextType;
use Oro\Bundle\FormBundle\Form\Extension\StripTagsExtension;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;

use Marello\Bundle\InventoryBundle\Entity\DeliveryPromise;

class DeliveryPromiseType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_inventory_delivery_promise';

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
                    'label' => 'marello.inventory.deliverypromise.code.label'
                ]
            )
            ->add(
                'labels',
                LocalizedFallbackValueCollectionType::class,
                [
                    'required' => true,
                    'label' => 'marello.inventory.deliverypromise.labels.label',
                    'entry_options' => [
                        'constraints' => [new NotBlank()],
                        StripTagsExtension::OPTION_NAME => true
                    ]
                ]
            )
            ->add(
                'minDays',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'marello.inventory.deliverypromise.min_days.label'
                ]
            )
            ->add(
                'maxDays',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'marello.inventory.deliverypromise.max_days.label'
                ]
            )
            ->add(
                'tooltips',
                LocalizedFallbackValueCollectionType::class,
                [
                    'required' => false,
                    'label' => 'marello.inventory.deliverypromise.tooltips.label',
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
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => DeliveryPromise::class,
            'constraints' => [
                new Valid()
            ]
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
