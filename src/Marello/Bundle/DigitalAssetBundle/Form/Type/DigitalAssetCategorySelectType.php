<?php

namespace Marello\Bundle\DigitalAssetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Oro\Bundle\FormBundle\Form\Type\OroEntitySelectOrCreateInlineType;

class DigitalAssetCategorySelectType extends AbstractType
{
    const BLOCK_PREFIX = 'marello_digital_asset_category_select';

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'autocomplete_alias' => 'marello_digital_asset_category',
                'create_form_route'  => 'marello_digital_asset_category_create',
                'grid_name'          => 'marello-digital-asset-category-select-grid',
                'create_enabled'     => true,
                'configs'            => [
                    'placeholder' => 'marello.digitalasset.category.placeholder',
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