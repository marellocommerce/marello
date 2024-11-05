<?php

namespace Marello\Bundle\InventoryBundle\Tests\Unit\EventListener;

use Marello\Bundle\InventoryBundle\Event\InventoryUpdateWebhookEvent;
use PHPUnit\Framework\TestCase;

use Marello\Bundle\WebhookBundle\Manager\WebhookProducer;
use Marello\Bundle\InventoryBundle\Manager\InventoryManager;
use Marello\Bundle\InventoryBundle\Event\InventoryUpdateEvent;
use Marello\Bundle\InventoryBundle\Model\InventoryUpdateContext;
use Marello\Bundle\InventoryBundle\Manager\BalancedInventoryManager;
use Marello\Bundle\InventoryBundle\EventListener\InventoryUpdateEventListener;

class InventoryUpdateEventListenerTest extends TestCase
{
    /**
     * @var InventoryUpdateContext|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $inventoryUpdateContext;

    /**
     * @var InventoryUpdateEventListener
     */
    protected $listener;

    /**
     * @var InventoryManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $inventoryManager;

    /**
     * @var BalancedInventoryManager|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $balancedInventoryManager;

    /**
     * @var WebhookProducer|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $webhookProducer;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->inventoryUpdateContext = new InventoryUpdateContext();
        $this->inventoryManager = $this
            ->getMockBuilder(InventoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->balancedInventoryManager = $this
            ->getMockBuilder(BalancedInventoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->webhookProducer = $this
            ->getMockBuilder(WebhookProducer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->listener = new InventoryUpdateEventListener(
            $this->inventoryManager,
            $this->balancedInventoryManager,
            $this->webhookProducer
        );
    }

    public function testHandleUpdateInventoryEvent()
    {
        $event = $this->prepareEvent();
        $this->inventoryManager->expects($this->once())
            ->method('updateInventoryLevel')
            ->with($this->inventoryUpdateContext);

        $webhookEvent = new InventoryUpdateWebhookEvent($event->getInventoryUpdateContext());
        $this->webhookProducer->expects($this->once())
            ->method('triggerWebhook')
            ->with($webhookEvent);

        $this->listener->handleUpdateInventoryEvent($event);
    }

    public function testHandleEventWithVirtualInventoryContext()
    {
        $this->inventoryUpdateContext->setIsVirtual(true);
        $event = $this->prepareEvent();
        $webhookEvent = new InventoryUpdateWebhookEvent($event->getInventoryUpdateContext());
        $this->webhookProducer->expects($this->once())
            ->method('triggerWebhook')
            ->with($webhookEvent);
        $this->balancedInventoryManager->expects($this->once())
            ->method('updateInventoryLevel')
            ->with($this->inventoryUpdateContext);

        $this->listener->handleUpdateInventoryEvent($event);
    }

    /**
     * @return InventoryUpdateEvent
     */
    protected function prepareEvent()
    {
        return new InventoryUpdateEvent($this->inventoryUpdateContext);
    }
}
