<?php

namespace Marello\Bundle\CatalogBundle\Form\Type;

use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\CatalogBundle\Formatter\CategoryCodeFormatter;
use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\FormBundle\Form\Type\EntityIdentifierType;
use Oro\Bundle\FormBundle\Form\Extension\StripTagsExtension;
use Oro\Bundle\LocaleBundle\Form\Type\LocalizedFallbackValueCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_catalog_category';

    /**
     * @var CategoryCodeFormatter
     */
    private $codeFormatter;

    /**
     * @param CategoryCodeFormatter $codeFormatter
     */
    public function __construct(CategoryCodeFormatter $codeFormatter)
    {
        $this->codeFormatter = $codeFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'names',
                LocalizedFallbackValueCollectionType::class,
                [
                    'label' => 'marello.catalog.category.names.label',
                    'required' => true,
                    'entry_options' => [
                        'constraints' => [new NotBlank(['message' => 'marello.catalog.category.messages.error.names.blank'])],
                        StripTagsExtension::OPTION_NAME => true,
                    ],
                ]
            )
            ->add('code', TextType::class)
            ->add(
                'descriptions',
                LocalizedFallbackValueCollectionType::class,
                [
                    'label' => 'marello.catalog.category.descriptions.label',
                    'required' => false,
                    'entry_type' => TextareaType::class,
                    'field' => 'text',
                ]
            )
            ->add(
                'appendProducts',
                EntityIdentifierType::class,
                [
                    'class'    => Product::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            )
            ->add(
                'removeProducts',
                EntityIdentifierType::class,
                [
                    'class'    => Product::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            );
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'preSubmit']);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        if (isset($data['code'])) {
            $data['code'] = $this->codeFormatter->format($data['code']);
            $event->setData($data);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'intention' => 'category',
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
