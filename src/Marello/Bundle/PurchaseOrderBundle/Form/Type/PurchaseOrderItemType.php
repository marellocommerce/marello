<?php

namespace Marello\Bundle\PurchaseOrderBundle\Form\Type;

use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Oro\Bundle\FormBundle\Form\Type\OroDateType;

use Marello\Bundle\InventoryBundle\Entity\InventoryBatch;
use Marello\Bundle\PricingBundle\Form\Type\ProductPriceType;
use Marello\Bundle\PurchaseOrderBundle\Entity\PurchaseOrderItem;
use Marello\Bundle\ProductBundle\Form\Type\ProductSupplierSelectType;
use Marello\Bundle\PurchaseOrderBundle\Validator\Constraints\PurchaseOrderItemConstraint;

class PurchaseOrderItemType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_purchase_order_item';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', ProductSupplierSelectType::class, [
                'label'          => 'marello.product.entity_label',
                'create_enabled' => false,
            ])
            ->add('orderedAmount', NumberType::class, [
                'label' => 'Ordered Amount'
            ])
            ->add('purchasePrice', ProductPriceType::class, [
                'label' => 'Purchase Price',
                'currency' => $options['currency'],
                'currency_symbol' => $options['currency_symbol']
            ])
            ->add(
                'requestedDeliveryDate',
                OroDateType::class,
                [
                    'required' => false,
                    'label' => 'marello.purchaseorder.purchaseorderitem.requested_delivery_date.label',
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'currency' => null,
            'currency_symbol' => null,
            'data_class' => PurchaseOrderItem::class,
            'constraints' => [
                new PurchaseOrderItemConstraint()
            ],
        ]);
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     * @return void
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        $vars = $view->vars;
        $value = $vars['value'];
        $vars['ooDReserved'] = null;
        if ($value instanceof PurchaseOrderItem) {
            $vars['ooDReserved'] = $value->getData();
        }
        $view->vars = $vars;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
