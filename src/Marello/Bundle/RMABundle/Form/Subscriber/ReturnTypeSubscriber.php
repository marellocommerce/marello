<?php

namespace Marello\Bundle\RMABundle\Form\Subscriber;

use Marello\Component\Order\Model\OrderItemInterface;
use Marello\Component\RMA\Entity\ReturnItem;
use Marello\Component\RMA\Model\ReturnEntityInterface;
use Marello\Component\RMA\Model\ReturnItemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ReturnTypeSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::SUBMIT       => 'onSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event)
    {
        /** @var ReturnEntityInterface $return */
        $return = $event->getData();

        $return->getOrder()
            ->getItems()
            ->map(function (OrderItemInterface $orderItem) use ($return) {
                $return->addReturnItem(new ReturnItem($orderItem));
            });

        $event->setData($return);
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        /** @var ReturnEntityInterface $return */
        $return = $event->getData();

        /*
         * Remove all return items with returned quantity equal to 0.
         */
        $return->getReturnItems()
            ->filter(function (ReturnItemInterface $returnItem) {
                return !$returnItem->getQuantity();
            })
            ->map(function (ReturnItemInterface $returnItem) use ($return) {
                $return->removeReturnItem($returnItem);
            });

        $event->setData($return);
    }
}
