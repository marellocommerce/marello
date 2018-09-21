<?php

namespace Marello\Bundle\ShippingBundle\Form\EventSubscriber;

use Marello\Bundle\ShippingBundle\Entity\ShippingMethodConfig;
use Marello\Bundle\ShippingBundle\Form\Type\ShippingMethodTypeConfigCollectionType;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class MethodConfigSubscriber implements EventSubscriberInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @var ShippingMethodProviderInterface
     */
    protected $shippingMethodProvider;

    /**
     * @param FormFactoryInterface            $factory
     * @param ShippingMethodProviderInterface $shippingMethodProvider
     */
    public function __construct(FormFactoryInterface $factory, ShippingMethodProviderInterface $shippingMethodProvider)
    {
        $this->factory = $factory;
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
        /** @var ShippingMethodConfig $data */
        $data = $event->getData();
        if (!$data) {
            return;
        }
        $this->recreateDynamicChildren($event->getForm(), $data->getMethod());
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $submittedData = $event->getData();
        $form = $event->getForm();
        /** @var ShippingMethodConfig $data */
        $data = $form->getData();

        if (!$data) {
            $this->recreateDynamicChildren($form, $submittedData['method']);
            $event->setData($submittedData);
        }
    }

    /**
     * @param FormInterface $form
     * @param string $method
     */
    protected function recreateDynamicChildren(FormInterface $form, $method)
    {
        $shippingMethod = $this->shippingMethodProvider->getShippingMethod($method);
        $oldOptions = $form->get('typeConfigs')->getConfig()->getOptions();
        $form->add('typeConfigs', ShippingMethodTypeConfigCollectionType::class, array_merge($oldOptions, [
            'is_grouped' => $shippingMethod->isGrouped(),
        ]));

        $oldOptions = $form->get('options')->getConfig()->getOptions();
        $child = $this->factory->createNamed('options', $shippingMethod->getOptionsConfigurationFormType());
        $form->add('options', $shippingMethod->getOptionsConfigurationFormType(), array_merge($oldOptions, [
            'compound' => $child->getConfig()->getOptions()['compound']
        ]));
    }
}
