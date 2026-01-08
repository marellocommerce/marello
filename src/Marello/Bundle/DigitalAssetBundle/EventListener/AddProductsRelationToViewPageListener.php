<?php

namespace Marello\Bundle\DigitalAssetBundle\EventListener;

use Oro\Bundle\UIBundle\Event\BeforeFormRenderEvent;
use Oro\Bundle\UIBundle\View\ScrollData;

class AddProductsRelationToViewPageListener
{
    public function addAssetProductsRelation(BeforeFormRenderEvent $event): void
    {
        $form = $event->getForm();
        if (!isset($form['asset_products_rel'])) {
            return;
        }

        $scrollData = new ScrollData($event->getFormData());
        $scrollData->addSubBlockData(
            0,
            0,
            $event->getTwigEnvironment()->render(
                '@MarelloDigitalAsset/DigitalAsset/assetProductRelation.html.twig',
                ['form' => $form]
            )
        );
        $event->setFormData($scrollData->getData());
    }
}
