<?php

namespace Marello\Bundle\InventoryBundle\Tests\Unit\EventListener;

use PHPUnit\Framework\TestCase;

use Marello\Bundle\WebhookBundle\Manager\WebhookProducer;
use Marello\Bundle\InventoryBundle\Manager\InventoryManager;
use Marello\Bundle\InventoryBundle\Event\InventoryUpdateEvent;
use Marello\Bundle\InventoryBundle\Model\InventoryUpdateContext;
use Marello\Bundle\InventoryBundle\Manager\BalancedInventoryManager;
use Marello\Bundle\InventoryBundle\Event\InventoryUpdateWebhookEvent;
use Marello\Bundle\InventoryBundle\EventListener\InventoryUpdateEventListener;

class InventoryLevelRebalanceEventListenerTest extends TestCase
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
        // todo:
        // create test(s) that will cover the inventory level update and trigger of the balancer
        // check that it does cover both the update as the trigger for the balancer.
    }

    public function testHandleUpdateInventoryEvent()
    {

    }
}
