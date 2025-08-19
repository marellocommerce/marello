<?php

namespace Marello\Bundle\CatalogBundle\Form\Type;

use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\CatalogBundle\Formatter\CategoryCodeFormatter;
use Marello\Bundle\CustomerBundle\Entity\Company;
use Marello\Bundle\CustomerBundle\Form\Type\CompanyAwareCustomerSelectType;
use Marello\Bundle\CustomerBundle\Form\Type\CompanySelectType;
use Marello\Bundle\CustomerBundle\Form\Type\CustomerSelectType;
use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\FormBundle\Form\Type\EntityIdentifierType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Oro\Bundle\FormBundle\Utils\FormUtils;
use Symfony\Component\Validator\Constraints\NotNull;

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
            ->add('name', TextType::class)
            ->add('code', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false
            ])
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
            )
            ->add('type')
            ->add(
                'customer',
                CompanyAwareCustomerSelectType::class,
                [
                    'required' => true,
                    'create_enabled' => false,
                    'constraints' => new NotNull()
                ]
            )
            ->add(
                'company',
                CompanySelectType::class,
                [
                    'mapped' => false,
                    'required' => false,
                    'create_enabled' => false
                ]
            )
            ->add(
                'appendCompanies',
                EntityIdentifierType::class,
                [
                    'class'    => Company::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            )
            ->add(
                'removeCompanies',
                EntityIdentifierType::class,
                [
                    'class'    => Company::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            );
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'preSubmit']);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSetDataListener']);
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
     * @param FormEvent $event
     */
    public function preSetDataListener(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data === null) {
            return;
        }

        // Only set type if category doesn't already have it
        if ($data->getType() === null) {
            $data->setType('default');
        } else {
            FormUtils::replaceField($form, 'type', ['disabled' => true]);
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
