<?php
//
//namespace Marello\Bundle\DigitalAssetBundle\DependencyInjection\Compiler;
//
//use Marello\Bundle\DigitalAssetBundle\Entity\DigitalAssetCategory;
//use Marello\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetCategorySelectType;
//use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
//use Symfony\Component\DependencyInjection\ContainerBuilder;

//class DigitalAssetCategoryExtendGuesserPass implements CompilerPassInterface
//{
//    #[\Override]
//    public function process(ContainerBuilder $container)
//    {
//        $guesser = $container->findDefinition('oro_entity_extend.provider.extend_field_form_type');
//        $guesser->addMethodCall(
//            'addExtendTypeMapping',
//            [
//                'manyToOne',
//                DigitalAssetCategorySelectType::class,
//                [
//                    'label' => 'marello.digitalasset.category.entity_label',
//                    'required' => false
//                ]
//            ]
//        );
//    }
//}