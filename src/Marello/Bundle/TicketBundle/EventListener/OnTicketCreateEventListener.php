<?php

namespace Marello\Bundle\TicketBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\UnitOfWork;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\TicketBundle\Entity\Ticket;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use function PHPUnit\Framework\isEmpty;

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
     * @var DoctrineHelper
     */
    protected $doctrineHelper;

    /**
     * @param DoctrineHelper $helper
     */
    public function __construct(DoctrineHelper $helper)
    {
        $this->doctrineHelper = $helper;
    }

    /**
     * Handle incoming event
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $this->em = $eventArgs->getObjectManager();
        $this->unitOfWork = $this->em->getUnitOfWork();

        if(!empty($this->unitOfWork->getScheduledEntityInsertions())) {
            $records = $this->filterRecords($this->unitOfWork->getScheduledEntityInsertions());
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

//        var_dump($ticket->getCustomer()->getFullName());
//        var_dump($ticket->getFirstName());
//        var_dump($ticket->getLastName());
//        var_dump($ticket->getEmail());
//        var_dump($ticket->getOwner()->getFirstName());
//        var_dump($ticket->getSource());
//        var_dump($ticket->getPriority());
//        var_dump($ticket->getCategory());
//        var_dump($ticket->getSubject());
//        var_dump($ticket->getDescription());

        if($customer)
        {
            $ticket = $ticket->setCustomer($customer);
            var_dump($ticket->getCustomer()->getFullName());
            var_dump($ticket->getFirstName());
            var_dump($ticket->getLastName());
            var_dump($ticket->getEmail());
            var_dump($ticket->getOwner()->getFirstName());
            var_dump($ticket->getSource());
            var_dump($ticket->getPriority());
            var_dump($ticket->getCategory());
            var_dump($ticket->getSubject());
            var_dump($ticket->getDescription());

            $this->em->persist($ticket);

            $classMeta = $this->em->getClassMetadata(get_class($ticket));
            $this->unitOfWork->computeChangeSet($classMeta, $ticket);
        }
    }
}