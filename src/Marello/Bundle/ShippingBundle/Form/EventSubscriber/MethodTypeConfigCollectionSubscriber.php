<?php

namespace Marello\Bundle\ShippingBundle\Form\EventSubscriber;

use Doctrine\Common\Collections\Collection;
use Marello\Bundle\ShippingBundle\Entity\ShippingMethodTypeConfig;
use Marello\Bundle\ShippingBundle\Form\Type\ShippingMethodTypeConfigType;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodInterface;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodProviderInterface;
use Marello\Bundle\ShippingBundle\Method\ShippingMethodTypeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class MethodTypeConfigCollectionSubscriber implements EventSubscriberInterface
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
        /** @var FormInterface|Form[] $form */
        $form = $event->getForm();
        /** @var Collection|ShippingMethodTypeConfig[] $data */
        $data = $event->getData();

        if (!$data) {
            return;
        }

        $methodConfig = $form->getParent()->getData();
        $method = $this->shippingMethodProvider->getShippingMethod($methodConfig->getMethod());

        $renderedTypes = [];
        foreach ($data as $index => $typeConfig) {
            $type = $method->getType($typeConfig->getType());
            if ($type) {
                $this->createTypeForm($form, $index, $method, $type);
                $renderedTypes[] = $type->getIdentifier();
            } else {
                $this->removeTypeForm($form, $index);
            }
        }

        $index = count($data);
        foreach ($method->getTypes() as $type) {
            if (null !== $type && !in_array($type->getIdentifier(), $renderedTypes, true)) {
                $this->createTypeForm($form, $index, $method, $type);
                $entity = new ShippingMethodTypeConfig();
                $entity->setType($type->getIdentifier())
                    ->setMethodConfig($methodConfig);
                $data->set($index, $entity);
                $index++;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preSubmit(FormEvent $event)
    {
        /** @var array $submittedData */
        $submittedData = $event->getData();
        $form = $event->getForm();

        if (!$event->getData()) {
            return;
        }

        $methodIdentifier = $form->getParent()->get('method')->getData();
        $method = $this->shippingMethodProvider->getShippingMethod($methodIdentifier);

        foreach ($submittedData as $index => $methodTypeData) {
            $type = $method->getType($methodTypeData['type']);
            if ($type) {
                $this->createTypeForm($form, $index, $method, $type);
            }
        }
    }

    /**
     * @param FormInterface $form
     * @param string $index
     * @param ShippingMethodInterface $method
     * @param ShippingMethodTypeInterface $type
     */
    protected function createTypeForm(
        FormInterface $form,
        $index,
        ShippingMethodInterface $method,
        ShippingMethodTypeInterface $type
    ) {
        $form->add($index, ShippingMethodTypeConfigType::class, [
            'options_type' => $type->getOptionsConfigurationFormType(),
            'auto_initialize' => false,
            'label' => $type->getLabel(),
            'is_grouped' => $method->isGrouped(),
        ]);
    }

    /**
     * @param FormInterface $form
     * @param string $index
     */
    protected function removeTypeForm(
        FormInterface $form,
        $index
    ) {
        $form->remove($index);
    }
}
