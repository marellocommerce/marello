<?php

namespace Marello\Bundle\InventoryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;

use Marello\Bundle\InventoryBundle\Entity\DeliveryPromise;

class DeliveryPromiseSelectType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_inventory_deliverypromise_select';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias'    => 'delivery_promise',
                'entity_class'          => DeliveryPromise::class,
                'create_enabled'        => false,
                'grid_name'             => 'marello-inventory-delivery-promise',
                'configs'               => [
                    'placeholder'               => 'marello.inventory.deliverypromise.form.choose_delivery_promise',
                    'result_template_twig'      => '@MarelloInventory/DeliveryPromise/Autocomplete/result.html.twig',
                    'selection_template_twig'   => '@MarelloInventory/DeliveryPromise/Autocomplete/selection.html.twig',
                ],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return OroEntitySelectOrCreateInlineType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return self::BLOCK_PREFIX;
    }
}
