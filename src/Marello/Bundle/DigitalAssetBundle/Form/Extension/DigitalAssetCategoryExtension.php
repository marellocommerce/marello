<?php

namespace Marello\Bundle\DigitalAssetBundle\Form\Extension;

use Marello\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetCategorySelectType;
use Oro\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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

        var_dump($builder->has('marello_digital_asset_category_rel'));

//        if ($builder->has('marello_digital_asset_category_rel')) {
//            $builder->remove('marello_digital_asset_category_rel');
//        }

        $builder->add('marello_digital_asset_category_rel', DigitalAssetCategorySelectType::class, [
                'label' => 'new label',
                'required' => false,
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            var_dump($form->has('marello_digital_asset_category_rel'));

//            if ($form->has('marello_digital_asset_category_rel')) {
//                $form->remove('marello_digital_asset_category_rel');
//            }
//
            $form->add('marello_digital_asset_category_rel', DigitalAssetCategorySelectType::class, [
                'label' => 'new label',
                'required' => false,
            ]);
        });
    }
}