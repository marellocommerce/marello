<?php

namespace Marello\Bundle\DigitalAssetBundle\Form\Extension;

use Marello\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetCategorySelectType;
use Oro\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class DigitalAssetCategoryExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [DigitalAssetType::class];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($builder->has('marello_digital_asset_category_rel')) {
            $builder->remove('marello_digital_asset_category_rel');
        }

        $builder->add(
            'marello_digital_asset_category_rel',
            DigitalAssetCategorySelectType::class,
            [
                'label' => 'category',
                'required' => false,
                'block' => 'general',
            ]
        );
    }
}