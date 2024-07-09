<?php

namespace Marello\Bundle\PurchaseOrderBundle\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Marello\Bundle\PurchaseOrderBundle\Cron\SendPurchaseOrderCommand;
use Marello\Bundle\PurchaseOrderBundle\Exception\LogicException;
use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;
use Oro\Bundle\CronBundle\Entity\Schedule;

class SendScheduleChangeListener
{
    public function __construct(
        private ManagerRegistry $registry
    ) {}

    public function onConfigUpdate(ConfigUpdateEvent $event): void
    {
        if (!$event->isChanged('marello_purchaseorder.sending_schedule')) {
            return;
        }

        $newDefinition = $event->getNewValue('marello_purchaseorder.sending_schedule');
        $schedule = $this->registry
            ->getRepository(Schedule::class)
            ->findOneBy(['command' => SendPurchaseOrderCommand::$defaultName]);
        if (!$schedule) {
            throw new LogicException('Send PurchaseOrder command not found, please run "oro:cron:definitions:load" command');
        }

        $schedule->setDefinition($newDefinition);
        $this->registry->getManagerForClass(Schedule::class)->flush();
    }
}
