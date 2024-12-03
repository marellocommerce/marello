<?php

namespace Marello\Bundle\TicketBundle\EventListener;

use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

use Marello\Bundle\TicketBundle\Entity\Ticket;
use Marello\Bundle\CustomerBundle\Entity\Customer;

class OnTicketCreateEventListener
{
    /**
     * @var UnitOfWork
     */
    protected $unitOfWork;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(protected DoctrineHelper $doctrineHelper)
    {
    }

    /**
     * Handle incoming event
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $this->em = $eventArgs->getObjectManager();
        $this->unitOfWork = $this->em->getUnitOfWork();

        if (!empty($this->unitOfWork->getScheduledEntityInsertions())) {
            $records = $this->filterRecords($this->unitOfWork->getScheduledEntityInsertions());
            $this->applyCallBackForChangeSet('assignCustomer', $records);
        }

        if (!empty($this->unitOfWork->getScheduledEntityUpdates())) {
            $records = $this->filterRecords($this->unitOfWork->getScheduledEntityUpdates());
            $this->applyCallBackForChangeSet('assignCustomer', $records);
        }
    }

    protected function filterRecords(array $records)
    {
        return array_filter($records, [$this, 'getIsEntityInstanceOf']);
    }

    /**
     * @param $entity
     * @return bool
     */
    public function getIsEntityInstanceOf($entity)
    {
        return ($entity instanceof Ticket);
    }

    /**
     * @param string $callback function
     * @param array $changeSet
     * @throws \Exception
     */
    protected function applyCallBackForChangeSet($callback, array $changeSet)
    {
        try {
            array_walk($changeSet, [$this, $callback]);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function assignCustomer(Ticket $ticket)
    {
        $email = $ticket->getEmail();

        $repo = $this->doctrineHelper
            ->getEntityManagerForClass(Customer::class)
            ->getRepository(Customer::class);
        $customer = $repo->findOneBy(['email'=> $email]);

        if ($customer) {
            $ticket = $ticket->setCustomer($customer);
            $this->em->persist($ticket);
            $classMeta = $this->em->getClassMetadata(get_class($ticket));
            $this->unitOfWork->recomputeSingleEntityChangeSet($classMeta, $ticket);
        }
    }
}
