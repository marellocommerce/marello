<?php

namespace Marello\Bundle\InventoryBundle\Tests\Unit\EventListener;

use PHPUnit\Framework\TestCase;

use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;

use Oro\Component\MessageQueue\Client\MessageProducerInterface;
use Oro\Bundle\MessageQueueBundle\Client\BufferedMessageProducer;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Entity\InventoryLevel;
use Marello\Bundle\InventoryBundle\Manager\InventoryItemManagerInterface;
use Marello\Bundle\InventoryBundle\Async\Topic\ResolveRebalanceInventoryTopic;
use Marello\Bundle\InventoryBundle\EventListener\Doctrine\InventoryLevelRebalanceEventListener;

class InventoryLevelRebalanceEventListenerTest extends TestCase
{
    /**
     * @var InventoryLevelRebalanceEventListener
     */
    protected $listener;

    /**
     * @var InventoryItemManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $inventoryItemManager;

    /**
     * @var EntityManagerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $entityManager;

    /**
     * @var MessageProducerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $producer;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->producer = $this->createMock(BufferedMessageProducer::class);

        $this->listener = new InventoryLevelRebalanceEventListener($this->producer);
    }

    public function testProductInventoryIsRebalancedAfterCreation()
    {
        $event = $this->prepareEvent();
        $uowMock = $this->createMock(UnitOfWork::class);

        $this->entityManager->expects($this->atLeastOnce())
            ->method('getUnitOfWork')
            ->willReturn($uowMock);

        $inventoryLevelMock = $this->createMock(InventoryLevel::class);
        $inventoryItemMock = $this->createMock(InventoryItem::class);
        $productMock = $this->createMock(Product::class);


        $uowMock->expects($this->atLeastOnce())
            ->method('getScheduledEntityInsertions')
            ->willReturn([$inventoryLevelMock]);

        $uowMock->expects($this->atLeastOnce())
            ->method('getScheduledEntityUpdates')
            ->willReturn([]);

        $inventoryLevelMock->expects($this->once())
            ->method('getInventoryItem')
            ->willReturn($inventoryItemMock);

        $inventoryItemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);

        $productMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);

        $this->producer->expects($this->once())
            ->method('enableBuffering');

        $this->producer->expects($this->once())
            ->method('send')
            ->with(ResolveRebalanceInventoryTopic::getName());

        $this->listener->onFlush($event);
    }

    public function testProductInventoryIsRebalancedAfterUpdate()
    {
        $event = $this->prepareEvent();
        $uowMock = $this->createMock(UnitOfWork::class);

        $this->entityManager->expects($this->atLeastOnce())
            ->method('getUnitOfWork')
            ->willReturn($uowMock);

        $inventoryLevelMock = $this->createMock(InventoryLevel::class);
        $inventoryItemMock = $this->createMock(InventoryItem::class);
        $productMock = $this->createMock(Product::class);


        $uowMock->expects($this->atLeastOnce())
            ->method('getScheduledEntityInsertions')
            ->willReturn([]);

        $uowMock->expects($this->atLeastOnce())
            ->method('getScheduledEntityUpdates')
            ->willReturn([$inventoryLevelMock]);

        $inventoryLevelMock->expects($this->once())
            ->method('getInventoryItem')
            ->willReturn($inventoryItemMock);

        $inventoryItemMock->expects($this->once())
            ->method('getProduct')
            ->willReturn($productMock);

        $productMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);

        $this->producer->expects($this->once())
            ->method('enableBuffering');

        $this->producer->expects($this->once())
            ->method('send')
            ->with(ResolveRebalanceInventoryTopic::getName());

        $this->listener->onFlush($event);
    }

    /**
     * @return OnFlushEventArgs
     */
    protected function prepareEvent()
    {
        return new OnFlushEventArgs($this->entityManager);
    }
}
