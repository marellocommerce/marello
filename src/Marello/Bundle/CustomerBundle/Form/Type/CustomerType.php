<?php

namespace Marello\Bundle\CustomerBundle\Form\Type;

use Oro\Bundle\CustomerBundle\Entity\CustomerUser;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Oro\Bundle\LocaleBundle\Form\Type\LocalizationSelectType;

use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\AddressBundle\Form\Type\AddressType;

class CustomerType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_customer';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', CompanySelectType::class, [
                'required' => false,
                'create_enabled' => false
            ])
            ->add('namePrefix', TextType::class, [
                'required' => false
            ])
            ->add('firstName', TextType::class, [
                'required'    => true
            ])
            ->add('middleName', TextType::class, [
                'required' => false
            ])
            ->add('lastName', TextType::class, [
                'required'    => true
            ])
            ->add('nameSuffix', TextType::class, [
                'required' => false
            ])
            ->add('email', EmailType::class, [
                'required'    => true
            ])
            ->add('customerNumber', TextType::class, [
                'required' => false
            ])
            ->add('customerGroup', CustomerGroupSelectType::class, [
                'required' => false,
                'create_enabled' => false
            ])
            ->add('localization', LocalizationSelectType::class, [
                'required' => false
            ])
            ->add('primaryAddress', CustomerPrimaryAddressType::class, [
                'required' => false
            ])
            ->add('shippingAddress', AddressType::class, [
                'required' => false
            ])
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'required' => false
                ]
            );

        $data = $builder->getData();
        $passwordOptions = [
            'type' => PasswordType::class,
            'required' => false,
            'first_options' => [
                'label' => 'marello.customer.frontend.password.label',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ],
            'second_options' => [
                'label' => 'marello.customer.frontend.password_confirmation.label',
            ],
            'invalid_message' => 'marello.customer.password_mismatch.message',
        ];

        if ($data instanceof Customer && $data->getId()) {
            $passwordOptions = array_merge($passwordOptions, ['required' => false]);
        } else {
            $builder
                ->add(
                    'passwordGenerate',
                    CheckboxType::class,
                    [
                        'required' => false,
                        'mapped' => false
                    ]
                )
                ->add(
                    'sendEmail',
                    CheckboxType::class,
                    [
                        'required' => false,
                        'mapped' => false
                    ]
                );
            $passwordOptions = array_merge($passwordOptions, ['required' => true, 'validation_groups' => ['create']]);
        }

        $builder->add('plainPassword', RepeatedType::class, $passwordOptions);


        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                if (!empty($data['primaryAddress'])
                    && isset($data['primaryAddress']['usePrimaryAddressAsShipping'])
                ) {
                    if ($data['primaryAddress']['usePrimaryAddressAsShipping'] === '1') {
                        $data['shippingAddress'] = $data['primaryAddress'];
                    }
                    $event->setData($data);
                }
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'           => Customer::class,
            'intention'            => 'customer',
            'extra_fields_message' => 'This form should not contain extra fields: "{{ extra_fields }}"',
            'constraints'          => [new Valid()],
            'allow_extra_fields'   => true
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
