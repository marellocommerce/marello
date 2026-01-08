<?php

namespace Marello\Bundle\DigitalAssetBundle\Form\Extension;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Oro\Bundle\FormBundle\Form\Type\EntityIdentifierType;
use Oro\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetType;

use Marello\Bundle\ProductBundle\Entity\Product;
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

        if ($builder->has('asset_products_rel')) {
            $builder->remove('asset_products_rel');
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

        $builder->add(
            'added',
            EntityIdentifierType::class,
            [
                'class'    => Product::class,
                'required' => false,
                'mapped'   => false,
                'multiple' => true,
            ]
        )->add(
            'removed',
            EntityIdentifierType::class,
            [
                'class'    => Product::class,
                'required' => false,
                'mapped'   => false,
                'multiple' => true,
            ]
        )
        ->add(
            'asset_products_rel',
            EntityType::class,
            [
                'class' => Product::class,
                'multiple' => true,
                'required' => false,
                'mapped' => true
            ]
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                var_dump($event->getForm()->get('added')->getData());
                die();
//                // Hack to set the same owner for OrderItem as for Order
//                // We need to fill not only data value, but also set this data for a form to avoid validation error
//                $parentOwnerField = $event->getForm()->getParent()->getParent()->get('owner');
//                $data = $parentOwnerField->getData();
//                $viewData = $parentOwnerField->getViewData();
//                $event->getForm()->get('owner')->setData($data);
//                $event->setData(['owner' => $viewData] + $event->getData());
            }
        );
    }
}
