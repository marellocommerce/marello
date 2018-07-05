<?php

namespace Marello\Bundle\InventoryBundle\Tests\Unit\Provider;

use Marello\Bundle\ProductBundle\Entity\ProductInterface;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Doctrine\ORM\EntityManager;

use Marello\Bundle\InventoryBundle\Entity\Repository\VirtualInventoryRepository;
use Marello\Bundle\InventoryBundle\Provider\AvailableInventoryProvider;

class AvailableInventoryProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var DoctrineHelper|\PHPUnit_Framework_MockObject_MockObject $doctrineHelper */
    protected $doctrineHelper;


    public function setUp()
    {
        $this->doctrineHelper = $this->getMockBuilder(DoctrineHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'getEntityManagerForClass'
                ]
            )
            ->getMock();
    }

    public function testGetAvailableInventory()
    {
        $manager = $this->createMock(EntityManager::class);
        $repository = $this->createMock(VirtualInventoryRepository::class);
        $product = $this->createMock(ProductInterface::class);
        $salesChannel = $this->createMock(SalesChannel::class);
        $salesChannelGroup = $this->createMock(SalesChannelGroup::class);
        $this->doctrineHelper->expects($this->atLeastOnce())
            ->method('getEntityManagerForClass')
            ->willReturn($manager);
        $manager->expects($this->atLeastOnce())
            ->method('getRepository')
            ->willReturn($repository);

        $salesChannel->expects($this->once())
            ->method('getGroup')
            ->willReturn($salesChannelGroup);

        $repository->expects($this->atLeastOnce())
            ->method('findExistingVirtualInventory')
            ->willReturn(null);

        $provider = new AvailableInventoryProvider($this->doctrineHelper);

        $productSku = '1244';
        $salesChannelId = '2';
        $result = $provider->getAvailableInventory($productSku, $salesChannelId);
        $this->assertNotNull($result);
    }
}
