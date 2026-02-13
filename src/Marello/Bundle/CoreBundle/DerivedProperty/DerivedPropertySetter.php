<?php

namespace Marello\Bundle\CoreBundle\DerivedProperty;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Marello\Bundle\CoreBundle\Provider\SequenceNumberProvider;
use Marello\Bundle\OrderBundle\Entity\Order;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Oro\Bundle\EntityExtendBundle\Migration\EntityMetadataHelper;

class DerivedPropertySetter
{
    /** @var DerivedPropertyAwareInterface[] */
    private $generate = [];

    /**
     * DerivedPropertySetter constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
//        protected EntityMetadataHelper $entityMetadataHelper
    ) {
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em  = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        $insertions = $uow->getScheduledEntityInsertions();

        $this->generate = array_filter($insertions, function ($entity) {
            return $entity instanceof DerivedPropertyAwareInterface;
        });

        if (!empty($this->generate)) {
            $em->beginTransaction();
        }
    }

    /**
     * @param PostFlushEventArgs $args
     *
     * @throws \Exception
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (empty($this->generate)) {
            return;
        }

        foreach ($this->generate as $entity) {
            $sequenceName = SequenceNumberProvider::generateSequenceEntityName(
                $entity->getEntityType(),
                $entity->getOrganization()->getId()
            );
            $sequence = SequenceNumberProvider::generateSequenceEntity($sequenceName);
            $args->getObjectManager()->persist($sequence);
            $args->getObjectManager()->flush($sequence);
            $entity->setDerivedProperty($sequence->getId());
            $this->generate[] = $entity;
        }

        $dispatch = $this->generate;

        try {
            $args->getObjectManager()->flush($this->generate);
        } catch (\Exception $e) {
            $args->getObjectManager()->rollback();
            throw $e;
        }

        $args->getObjectManager()->commit();

        foreach ($dispatch as $entity) {
            $this->eventDispatcher->dispatch(new DerivedPropertySetEvent($entity), DerivedPropertySetEvent::NAME);
        }

        $this->generate = [];
    }
}
