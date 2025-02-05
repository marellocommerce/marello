<?php

namespace Marello\Bundle\ProductBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Oro\Bundle\FormBundle\Utils\FormUtils;

use Marello\Bundle\ProductBundle\Entity\Variant;

class VariantSubscriber implements EventSubscriberInterface
{
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
        $entity = $event->getData();
        $form   = $event->getForm();
        if (!$entity || null === $entity->getId()) {
            if ($form->has('variantCode')) {
                if ($entity instanceof Variant && count($entity->getProducts()) > 0) {
                    $parent = $entity->getProducts()->first();
                    $entity->setVariantCode($this->getVariantCode($parent->getSku()));
                    $event->setData($entity);
                }
            }
        }

        if ($entity && $form->has('variantCode')) {
            if ($entity->getVariantCode() !== null) {
                FormUtils::replaceField($form, 'variantCode', ['disabled' => true]);
            }
        }
    }

    /**
     * Generate md5 hash for input, will be used as variant code
     *
     * @param $input
     *
     * @return string
     */
    protected function getVariantCode($input)
    {
        $hash = hash('md5', $input);

        return substr($hash, 0, 10);
    }
}
