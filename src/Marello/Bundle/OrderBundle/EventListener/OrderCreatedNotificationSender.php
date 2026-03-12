<?php

namespace Marello\Bundle\OrderBundle\EventListener;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Component\DependencyInjection\ServiceLink;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Model\OrderItemTypeInterface;
use Marello\Bundle\CoreBundle\DerivedProperty\DerivedPropertySetEvent;

class OrderCreatedNotificationSender
{
    /**
     * @var ServiceLink
     */
    protected $emailSendProcessorLink;

    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var DoctrineHelper
     */
    protected $doctrineHelper;

    /**
     * @param ServiceLink $emailSendProcessorLink
     * @param ConfigManager $configManager
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(
        ServiceLink $emailSendProcessorLink,
        ConfigManager $configManager,
        DoctrineHelper $doctrineHelper
    ) {
        $this->emailSendProcessorLink = $emailSendProcessorLink;
        $this->configManager = $configManager;
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * @param DerivedPropertySetEvent $event
     */
    public function derivedPropertySet(DerivedPropertySetEvent $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Order) {
            $totalItemsCandC = 0;
            foreach ($entity->getItems() as $item) {
                if ($item->getItemType() === OrderItemTypeInterface::OI_TYPE_CASHANDCARRY) {
                    $totalItemsCandC++;
                }
            }
            // not all items are cash and carry, so send an email when the order is created
            if ($totalItemsCandC !== $entity->getItems()->count() &&
                $this->configManager->get('marello_order.confirmation_email_template')
            ) {
                $this->sendNotification($entity);
            }

            // persist and flush the entity as some properties might not be set properly
            $manager = $this->doctrineHelper
                ->getEntityManagerForClass(Order::class);
            $manager->persist($entity);
            $manager->flush();
        }
    }

    /**
     * @param Order $order
     */
    protected function sendNotification(Order $order)
    {
        $this->emailSendProcessorLink->getService()->sendNotification(
            $this->configManager->get('marello_order.confirmation_email_template'),
            [$order->getCustomer()],
            $order
        );
    }
}
