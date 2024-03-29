<?php

namespace Marello\Bundle\PurchaseOrderBundle\Tests\Unit\Processor;

use Doctrine\Persistence\ObjectManager;

use PHPUnit\Framework\TestCase;

use Oro\Bundle\NoteBundle\Entity\Note;

use Marello\Bundle\PurchaseOrderBundle\Entity\PurchaseOrder;
use Marello\Bundle\PurchaseOrderBundle\Entity\PurchaseOrderItem;
use Marello\Bundle\PurchaseOrderBundle\Processor\NoteActivityProcessor;

class NoteActivityProcessorTest extends TestCase
{
    /** @var NoteActivityProcessor $processor */
    protected $processor;

    /** @var Note $noteMock */
    protected $noteMock;

    /** @var ObjectManager $entityManager */
    protected $entityManager;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->entityManager = $this
            ->getMockBuilder(ObjectManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->noteMock = $this->createMock(Note::class);
        $this->processor = new NoteActivityProcessor($this->noteMock, $this->entityManager);
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(): void
    {
        unset($this->processor);
    }

    /**
     * @test
     */
    public function addNoteCorrectly()
    {
        $purchaseOrder = $this
            ->getMockBuilder(PurchaseOrder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $items = $this->createPurchaseItems();

        $this->noteMock
            ->expects($this->once())
            ->method('setMessage');

        $this->noteMock
            ->expects($this->once())
            ->method('addActivityTarget')
            ->with($purchaseOrder);

        $this->entityManager
            ->expects($this->once())
            ->method('persist');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->processor->addNote($purchaseOrder, $items);
    }

    /**
     * @test
     */
    public function handleNonObjectString()
    {
        $this->expectException(\Exception::class);
        $this->processor->addNote('string', []);
    }

    /**
     * @test
     */
    public function handleNonObjectFloat()
    {
        $this->expectException(\Exception::class);
        $this->processor->addNote(1.2000, []);
    }

    /**
     * @test
     */
    public function handleNonObjectInteger()
    {
        $this->expectException(\Exception::class);
        $this->processor->addNote(5, []);
    }

    /**
     * @test
     */
    public function handleNonObjectArray()
    {
        $this->expectException(\Exception::class);
        $this->processor->addNote(['item1', 'item2'], []);
    }

    /**
     * @test
     */
    public function getEmptyMessageForActivity()
    {
        $items = [];

        $result = $this->processor->getMessage($items);
        $this->assertEmpty($result, 'No message has been created');
    }

    /**
     * @test
     */
    public function getMessageForActivity()
    {
        $items = $this->createPurchaseItems();

        $result = $this->processor->getMessage($items);
        $this->assertStringContainsString(
            'Quantities received for: ',
            $result,
            'Message contains "Quantities received for: "'
        );
    }

    /**
     * Create PurchaseOrderItems
     * @return array
     */
    private function createPurchaseItems()
    {
        $purchaseOrderItem = $this
            ->getMockBuilder(PurchaseOrderItem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $purchaseOrderItem->expects($this->once())
            ->method('getProductName')
            ->willReturn('Test Product');

        $items = [
            [
                'item' => $purchaseOrderItem,
                'qty' => 1
            ]
        ];

        return $items;
    }
}
