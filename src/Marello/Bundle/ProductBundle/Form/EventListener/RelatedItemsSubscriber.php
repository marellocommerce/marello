<?php

namespace Marello\Bundle\ProductBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\FormBundle\Form\Type\EntityIdentifierType;

class RelatedItemsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private AuthorizationCheckerInterface $authorizationChecker
    ) {
    }

    /**
     * Get subscribed events
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * Preset data for channels.
     *
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        if ($this->authorizationChecker->isGranted('marello_product_update')) {
            $form->add(
                'appendRelated',
                EntityIdentifierType::class,
                [
                    'class' => Product::class,
                    'required' => false,
                    'mapped' => false,
                    'multiple' => true,
                ]
            );
            $form->add(
                'removeRelated',
                EntityIdentifierType::class,
                [
                    'class' => Product::class,
                    'required' => false,
                    'mapped' => false,
                    'multiple' => true,
                ]
            );
        } else {
            $form->remove('appendRelated');
            $form->remove('removeRelated');
        }

        if ($this->authorizationChecker->isGranted('marello_product_update')) {
            $form->add(
                'appendUpsell',
                EntityIdentifierType::class,
                [
                    'class' => Product::class,
                    'required' => false,
                    'mapped' => false,
                    'multiple' => true,
                ]
            );
            $form->add(
                'removeUpsell',
                EntityIdentifierType::class,
                [
                    'class' => Product::class,
                    'required' => false,
                    'mapped' => false,
                    'multiple' => true,
                ]
            );
        } else {
            $form->remove('appendUpsell');
            $form->remove('removeUpsell');
        }

        if ($this->authorizationChecker->isGranted('marello_product_update')) {
            $form->add(
                'appendCrosssell',
                EntityIdentifierType::class,
                [
                    'class' => Product::class,
                    'required' => false,
                    'mapped' => false,
                    'multiple' => true,
                ]
            );
            $form->add(
                'removeCrosssell',
                EntityIdentifierType::class,
                [
                    'class' => Product::class,
                    'required' => false,
                    'mapped' => false,
                    'multiple' => true,
                ]
            );
        } else {
            $form->remove('appendCrosssell');
            $form->remove('removeCrosssell');
        }
    }
}
