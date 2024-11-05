<?php

namespace Marello\Bundle\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Oro\Bundle\FormBundle\Form\Type\EntityIdentifierType;

use Marello\Bundle\SalesBundle\Entity\SalesChannel;

class ProductsAssignSalesChannelsType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_products_assign_sales_channels';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'inset',
                HiddenType::class,
                [
                    'required' => true,
                    'label'    => 'marello.product.name.label',
                ]
            )
            ->add(
                'products',
                HiddenType::class,
                [
                    'required' => true,
                    'label'    => 'marello.product.sku.label',
                ]
            )
            ->add(
                'filters',
                HiddenType::class,
                [
                    'required' => false,
                    'label'    => 'marello.product.manufacturing_code.label',
                ]
            )
            ->add(
                'addSalesChannels',
                EntityIdentifierType::class,
                [
                    'class'    => SalesChannel::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            )
            ->add(
                'removeSalesChannels',
                EntityIdentifierType::class,
                [
                    'class'    => SalesChannel::class,
                    'required' => false,
                    'mapped'   => false,
                    'multiple' => true,
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
