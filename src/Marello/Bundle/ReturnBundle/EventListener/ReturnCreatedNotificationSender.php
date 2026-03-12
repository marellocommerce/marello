<?php

namespace Marello\Bundle\ReturnBundle\EventListener;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Component\DependencyInjection\ServiceLink;

use Marello\Bundle\ReturnBundle\Entity\ReturnEntity;
use Marello\Bundle\CoreBundle\DerivedProperty\DerivedPropertySetEvent;

class ReturnCreatedNotificationSender
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

        if ($entity instanceof ReturnEntity) {
            if ($this->configManager->get('marello_return.created_template')) {
                $this->sendNotification($entity);
            }
            // persist and flush the entity as some properties might not be set properly
            $manager = $this->doctrineHelper
                ->getEntityManagerForClass(ReturnEntity::class);
            $manager->persist($entity);
            $manager->flush();
        }
    }

    /**
     * @param ReturnEntity $returnEntity
     */
    protected function sendNotification(ReturnEntity $returnEntity)
    {
        $this->emailSendProcessorLink->getService()->sendNotification(
            $this->configManager->get('marello_return.created_template'),
            [$returnEntity->getOrder()->getCustomer()],
            $returnEntity
        );
    }
}
