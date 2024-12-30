<?php

namespace Marello\Bundle\CustomerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Marello\Bundle\AddressBundle\Form\Type\AddressType;

class CustomerPrimaryAddressType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_customer_primary_address';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'usePrimaryAddressAsShipping',
                CheckboxType::class,
                [
                    'label' => 'marello.customer.primary_address.use_as_shipping.label',
                    'required' => false,
                    'mapped' => false,
                    'priority' => 100
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return AddressType::class;
    }
}
