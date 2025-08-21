<?php

namespace Marello\Bundle\DigitalAssetBundle\Form\Extension;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Oro\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetType;

use Marello\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetCategorySelectType;

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

        if ($builder->has('version')) {
            $builder->remove('version');
        }

        $builder->add(
            'marello_digital_asset_category_rel',
            DigitalAssetCategorySelectType::class,
            [
                'label' => 'marello.digitalasset.category.label',
                'required' => false,
                'block' => 'general'
            ]
        );

        $builder->add(
            'version',
            TextType::class,
            [
                'label' => 'oro.digitalasset.version.label',
                'required' => false,
                'block' => 'general'
            ]
        );
    }
}
