<?php

namespace Marello\Bundle\ShippingBundle\Form\EventSubscriber;

use Doctrine\Common\Collections\Collection;
use Marello\Bundle\ShippingBundle\Entity\ShippingMethodConfig;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MethodConfigCollectionSubscriber implements EventSubscriberInterface
{
    /**
     * @var ShippingMethodProviderInterface
     */
    protected $shippingMethodProvider;

    /**
     * @param ShippingMethodProviderInterface $shippingMethodProvider
     */
    public function __construct(ShippingMethodProviderInterface $shippingMethodProvider)
    {
        $this->shippingMethodProvider = $shippingMethodProvider;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSet',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSet(FormEvent $event)
    {
        /** @var Collection|ShippingMethodConfig[] $data */
        $data = $event->getData();
        $form = $event->getForm();

        if (!$data) {
            return;
        }

        foreach ($data as $index => $methodConfig) {
            $shippingMethod = $this->shippingMethodProvider->getShippingMethod($methodConfig->getMethod());
            if (!$shippingMethod) {
                $data->remove($index);
                $form->remove($index);
            }
        }
        $event->setData($data);
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        /** @var array $data */
        $submittedData = $event->getData();
        $form = $event->getForm();

        if (!$submittedData) {
            return;
        }

        $filteredSubmittedData = [];
        foreach ($submittedData as $index => $itemData) {
            if (array_key_exists('method', $itemData)
                && $this->shippingMethodProvider->getShippingMethod($itemData['method']) !== null
            ) {
                $filteredSubmittedData[$index] = $itemData;
            } else {
                $form->remove($index);
            }
        }

        $event->setData($filteredSubmittedData);
    }
}
