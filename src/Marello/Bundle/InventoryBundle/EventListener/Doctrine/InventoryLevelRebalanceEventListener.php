<?php

namespace Marello\Bundle\InventoryBundle\EventListener\Doctrine;

use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;

use Oro\Component\MessageQueue\Client\Message;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

use Marello\Bundle\InventoryBundle\Async\Topics;
use Marello\Bundle\CoreBundle\Model\JobIdGenerationTrait;
use Marello\Bundle\InventoryBundle\Entity\InventoryLevel;
use Marello\Bundle\ProductBundle\Entity\ProductInterface;
use Marello\Bundle\InventoryBundle\Async\Topic\ResolveRebalanceInventoryTopic;

class InventoryLevelRebalanceEventListener
{
    use JobIdGenerationTrait;

    /** @var UnitOfWork $unitOfWork */
    protected $unitOfWork;

    /** @var EntityManager $em */
    protected $em;

    /** @var MessageProducerInterface $messageProducer */
    private $messageProducer;

    /**
     * OnInventoryItemCreateEventListener constructor.
     * @param MessageProducerInterface $messageProducer
     */
    public function __construct(MessageProducerInterface $messageProducer)
    {
        $this->messageProducer = $messageProducer;
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
            $this->applyCallBackForChangeSet('triggerRebalance', $records);
        }

        if (!empty($this->unitOfWork->getScheduledEntityUpdates())) {
            $records = $this->filterRecords($this->unitOfWork->getScheduledEntityUpdates());
            $this->applyCallBackForChangeSet('triggerRebalance', $records);
        }
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
        return ($entity instanceof InventoryLevel);
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

    /**
     * @param InventoryLevel $entity
     */
    protected function triggerRebalance(ProductInterface $entity)
    {
        /** @var ProductInterface $product */
        $product = $entity->getInventoryItem()->getProduct();
        $message = new Message([
            'product_id' => $product->getId(),
            'jobId' => $this->generateJobId($product->getId())
        ]);

        $this->messageProducer->enableBuffering();
        $this->messageProducer->send(
            ResolveRebalanceInventoryTopic::getName(),
            $message
        );
    }
}
