<?php

namespace Marello\Bundle\SupplierBundle\Form\Type;

use Marello\Bundle\AddressBundle\Form\Type\AddressType;
use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Oro\Bundle\CurrencyBundle\Form\Type\CurrencyType;
use Oro\Bundle\FormBundle\Utils\FormUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

class SupplierType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_supplier_form';

    public static $nonStreetAttributes = [
        'namePrefix',
        'firstName',
        'middleName',
        'lastName',
        'nameSuffix'
    ];

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                ['constraints' => new NotNull()]
            )
            ->add(
                'code',
                TextType::class,
                ['constraints' => new NotNull()]
            )
            ->add(
                'address',
                AddressType::class,
                ['required' => true]
            )
            ->add(
                'priority',
                TextType::class,
                ['constraints' => new NotNull()]
            )
            ->add('canDropship')
            ->add('isActive')
            ->add('email')
            ->add('currency', CurrencyType::class)
            ->add(
                'poSendBy',
                ChoiceType::class,
                [
                    'label' => 'marello.supplier.po_send_by.label',
                    'mapped' => true,
                    'choices' => [
                        'marello.supplier.po_send_by.manual' => Supplier::SEND_PO_MANUALLY,
                        'marello.supplier.po_send_by.email' => Supplier::SEND_PO_BY_EMAIL
                    ]
                ]
            )
            ->addEventListener(
                FormEvents::PRE_SET_DATA,
                [$this, 'preSetDataListener']
            );

        $this->removeNonStreetFieldsFromAddress($builder, 'address');
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['data-page-component-module'] = 'marellosupplier/js/app/components/supplier-component';
        $view->vars['attr']['data-page-component-options'] = json_encode(['autoRender' => true]);
    }

    public function preSetDataListener(FormEvent $event)
    {
        /** @var Supplier $supplier */
        $supplier = $event->getData();
        $form = $event->getForm();
        if ($supplier->getCode() !== null) {
            // disable code field for suppliers
            FormUtils::replaceField($form, 'code', ['disabled' => true]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => Supplier::class,
            'constraints'        => [new Valid()],
        ]);
    }

    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }

    protected function removeNonStreetFieldsFromAddress(FormBuilderInterface $builder, string $childName)
    {
        $address = $builder->get($childName);
        foreach (self::$nonStreetAttributes as $attribute) {
            $address->remove($attribute);
        }
    }
}
