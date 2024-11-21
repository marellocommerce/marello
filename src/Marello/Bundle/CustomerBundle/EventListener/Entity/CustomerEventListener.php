<?php

namespace  Marello\Bundle\CustomerBundle\EventListener\Entity;

use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;

use Marello\Bundle\CustomerBundle\Entity\Customer;

class CustomerEventListener
{
    /**
     * Handle incoming event
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        /** @var EntityManager $em */
        $em = $eventArgs->getObjectManager();
        /** @var UnitOfWork $unitOfWork */
        $unitOfWork = $em->getUnitOfWork();

        if (!empty($unitOfWork->getScheduledEntityInsertions())) {
            $records = $this->filterRecords($unitOfWork->getScheduledEntityInsertions());
            $this->applyCallBackForChangeSet('updateEmailLowerCase', $records);
        }
        if (!empty($unitOfWork->getScheduledEntityUpdates())) {
            $records = $this->filterRecords($unitOfWork->getScheduledEntityUpdates());
            $this->applyCallBackForChangeSet('updateEmailLowerCase', $records);
        }
    }

    /**
     * @param Customer $entity
     */
    protected function updateEmailLowerCase(Customer $entity)
    {
        // make sure email is lower cased when saved...
        $entity->setEmail(mb_strtolower($entity->getEmail()));
        $entity->setEmailLowercase(mb_strtolower($entity->getEmail()));
    }

    /**
     * @param array $records
     * @return array
     */
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
        return ($entity instanceof Customer);
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
}
