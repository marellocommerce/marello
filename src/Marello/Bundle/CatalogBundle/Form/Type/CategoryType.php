<?php

namespace Marello\Bundle\CatalogBundle\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\CatalogBundle\Formatter\CategoryCodeFormatter;
use Marello\Bundle\CustomerBundle\Entity\Company;
use Marello\Bundle\CustomerBundle\Form\Type\CustomerSelectType;
use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\FormBundle\Form\Type\EntityIdentifierType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Oro\Bundle\FormBundle\Utils\FormUtils;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormError;

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
                CustomerSelectType::class,
                [
                    'required' => false,
                    'create_enabled' => false
                ]
            )
            ->add(
                'isPersonal',
                CheckboxType::class, [
                    'required' => false
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
        $builder->addEventListener(FormEvents::POST_SUBMIT, [$this, 'validateCustomerCategory']);
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
     * @param FormEvent $event
     */
    public function validateCustomerCategory(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $form->getData();

        $type = \is_array($data) ? ($data['type'] ?? null) : ($data->getType() ?? null);

        // Run validation only if type is customer
        if (!$data || $type !== 'customer') {
            return;
        }

        $customer = \is_array($data) ? ($data['customer'] ?? null) : ($data->getCustomer() ?? null);
        $isPersonal = \is_array($data) ? ($data['isPersonal'] ?? null) : ($data->isPersonal() ?? null);

        if (!$customer) {
            $form->get('customer')->addError(
                new FormError('This value should not be empty.')
            );
        }

        if ($isPersonal === false && $customer && $customer->getCompany() === null) {
            $form->get('customer')->addError(
                new FormError('The selected Customer must have a company when Category is not personal.')
            );
        }

        $data->setCompanies($customer->getCompany() ? new ArrayCollection([$customer->getCompany()]) : new ArrayCollection());
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
