<?php

namespace Marello\Bundle\PurchaseOrderBundle\EventListener;

use Doctrine\Persistence\ManagerRegistry;

use Oro\Bundle\ConfigBundle\Entity\ConfigValue;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Oro\Bundle\CronBundle\Entity\Schedule;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;

use Marello\Bundle\PurchaseOrderBundle\Exception\LogicException;
use Marello\Bundle\PurchaseOrderBundle\Cron\SendPurchaseOrderCommand;

class SendScheduleChangeListener
{
    public function __construct(
        private ManagerRegistry $registry,
        private TokenStorageInterface $tokenStorage,
        protected ConfigManager $configManager
    ) {
    }

    public function onConfigUpdate(ConfigUpdateEvent $event): void
    {
        if (!$event->isChanged('marello_purchaseorder.sending_time')) {
            return;
        }

        $schedule = $this->registry
            ->getRepository(Schedule::class)
            ->findOneBy(['command' => SendPurchaseOrderCommand::$defaultName]);
        if (!$schedule) {
            throw new LogicException(
                'Send PurchaseOrder command not found, please run "oro:cron:definitions:load" command'
            );
        }

        $time = $event->getNewValue('marello_purchaseorder.sending_time');
        // oro_locale.timezone -> identifier
        $timeZoneValue = $this->configManager
            ->get('oro_locale.timezone', false, false, $this->tokenStorage->getToken()->getUser());
        $dateTime = new \DateTime('now');
        // set timezone that is from the user and or the system (as the user configuring the setting assumes it's in his timezone)
//        $dateTime->setTimezone(new \DateTimeZone($timeZoneValue));

        $dateTime->setTimestamp($time);
        // convert to UTC timezone as it needs to be stored in UTC timezone for the cron definition
        $dateTime->setTimezone(new \DateTimeZone('UTC'));
        $newTime = new \DateTime('now');
        $newTime->setTimezone(new \DateTimeZone('UTC'));
        $schedule->setDefinition(
            sprintf(
                '%s %s * * *',
                ltrim($dateTime->format('i'), '0'),
                $dateTime->format('G')
            )
        );
        $this->registry->getManagerForClass(Schedule::class)->flush();
    }
}
