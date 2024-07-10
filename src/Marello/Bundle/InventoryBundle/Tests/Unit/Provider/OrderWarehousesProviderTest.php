<?php

namespace Marello\Bundle\InventoryBundle\Tests\Unit\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;

use Marello\Bundle\InventoryBundle\Entity\WarehouseGroup;
use PHPUnit\Framework\TestCase;

use Oro\Component\Testing\Unit\EntityTrait;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Entity\WarehouseType;
use Marello\Bundle\InventoryBundle\Entity\InventoryLevel;
use Marello\Bundle\InventoryBundle\Model\OrderWarehouseResult;
use Marello\Bundle\InventoryBundle\Entity\WarehouseChannelGroupLink;
use Marello\Bundle\InventoryBundle\Provider\OrderWarehousesProvider;
use Marello\Bundle\InventoryBundle\Strategy\WFA\WFAStrategiesRegistry;
use Marello\Bundle\InventoryBundle\Provider\AllocationExclusionInterface;
use Marello\Bundle\InventoryBundle\Provider\WarehouseTypeProviderInterface;
use Marello\Bundle\InventoryBundle\Strategy\WFA\Quantity\QuantityWFAStrategy;
use Marello\Bundle\InventoryBundle\Provider\Allocation\AllocationItemFilterInterface;
use Marello\Bundle\InventoryBundle\Entity\Repository\WarehouseChannelGroupLinkRepository;
use Marello\Bundle\InventoryBundle\Strategy\WFA\Quantity\Calculator\QtyWHCalculatorInterface;

class OrderWarehousesProviderTest extends TestCase
{
    use EntityTrait;

    /**
     * @var OrderWarehousesProvider
     */
    protected $orderWarehousesProvider;

    /** @var $doctrineHelper DoctrineHelper */
    protected $doctrineHelper;

    protected function setUp(): void
    {
        $this->qtyWHCalculator = $this->createMock(QtyWHCalculatorInterface::class);
        $this->configManager = $this->createMock(ConfigManager::class);
        $this->doctrineHelper = $this->createMock(DoctrineHelper::class);
        $this->quantityWFAStrategy = new QuantityWFAStrategy(
            $this->qtyWHCalculator,
            $this->doctrineHelper,
            $this->configManager
        );

        $allocationExclusion = $this->createMock(AllocationExclusionInterface::class);
        $allocationItemFilter = $this->createMock(AllocationItemFilterInterface::class);

        $this->quantityWFAStrategy->setAllocationExclusionProvider($allocationExclusion);
        $this->quantityWFAStrategy->setAllocationItemFilterProvider($allocationItemFilter);

        $wfaRegistry = $this->createMock(WFAStrategiesRegistry::class);
        $wfaRegistry->expects($this->any())
            ->method('getStrategy')
            ->with(QuantityWFAStrategy::IDENTIFIER)
            ->willReturn($this->quantityWFAStrategy);
        $this->orderWarehousesProvider = new OrderWarehousesProvider($wfaRegistry);
    }

    /**
     * @dataProvider getWarehousesForOrderDataProvider
     *
     * @param Order $order
     * @param array $expectedResult
     */
    public function testGetWarehousesForOrder(
        Order $order,
        ArrayCollection $warehouses,
        WarehouseGroup $group,
        array $expectedResult
    ) {
//        TODO:: fix me
        return;
        $warehouseChannelLinkRepository = $this->createMock(WarehouseChannelGroupLinkRepository::class);
        $this->doctrineHelper
            ->expects($this->any())
            ->method('getEntityRepositoryForClass')
            ->willReturn($warehouseChannelLinkRepository);

        $channelGroupLink = $this->createMock(WarehouseChannelGroupLink::class);
        $channelGroupLink
            ->expects($this->atLeastOnce())
            ->method('getWarehouseGroup')
            ->willReturn($group);

        $group
            ->expects($this->atLeastOnce())
            ->method('getWarehouses')
            ->willReturn($warehouses);

        $warehouseChannelLinkRepository
            ->expects($this->atLeastOnce())
            ->method('findLinkBySalesChannelGroup')
            ->with($order->getSalesChannel()->getGroup())
            ->willReturn($channelGroupLink);



        $actualResult = $this->orderWarehousesProvider->getWarehousesForOrder($order);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function getWarehousesForOrderDataProvider()
    {
        $preferredSupplier = new Supplier();
        $preferredSupplier
            ->setCanDropship(true)
            ->setName('supplier1');
        $notPreferredSupplier = new Supplier();
        $notPreferredSupplier
            ->setCanDropship(true)
            ->setName('supplier2');

        $product1 = new Product();
        $product1->setSku('SKU-1');
        $product2 = new Product();
        $product2->setSku('SKU-2');
        $product3 = new Product();
        $product3->setSku('SKU-3');
        $product4 = new Product();
        $product4
            ->setSku('SKU-4')
            ->setPreferredSupplier($preferredSupplier);

        $inventoryItem1 = new InventoryItem($product1);
        $inventoryItem3 = new InventoryItem($product3);
        $inventoryItem4 = new InventoryItem($product4);

        $warehouseGroup = $this->createMock(WarehouseGroup::class);
        $externalWarehouseType = new WarehouseType(WarehouseTypeProviderInterface::WAREHOUSE_TYPE_EXTERNAL);
        $globalWarehouseType = new WarehouseType(WarehouseTypeProviderInterface::WAREHOUSE_TYPE_GLOBAL);

        /** @var Warehouse $defaultWarehouse */
        $defaultWarehouse = $this->getEntity(Warehouse::class, ['id' => 1]);
        $defaultWarehouse
            ->setDefault(true)
            ->setCode('default_warehouse')
            ->setWarehouseType($globalWarehouseType)
            ->setGroup($warehouseGroup);
        /** @var Warehouse $externalNotPreferableWarehouse */
        $externalNotPreferableWarehouse = $this->getEntity(Warehouse::class, ['id' => 3]);
        $externalNotPreferableWarehouse
            ->setCode('supplier2_external_warehouse')
            ->setWarehouseType($externalWarehouseType)
            ->setGroup($warehouseGroup);
        /** @var Warehouse $externalPreferableWarehouse */
        $externalPreferableWarehouse = $this->getEntity(Warehouse::class, ['id' => 4]);
        $externalPreferableWarehouse
            ->setCode('supplier1_external_warehouse')
            ->setWarehouseType($externalWarehouseType)
            ->setGroup($warehouseGroup);

        /** @var InventoryLevel $inventoryLevel1 */
        $inventoryLevel1 = $this->getEntity(InventoryLevel::class, ['id' => 1]);
        $inventoryLevel1
            ->setWarehouse($defaultWarehouse)
            ->setInventoryQty(3);
        /** @var InventoryLevel $inventoryLevel2 */
        $inventoryLevel2 = $this->getEntity(InventoryLevel::class, ['id' => 2]);
        /** @var InventoryLevel $inventoryLevel3 */
        $inventoryLevel3 = $this->getEntity(InventoryLevel::class, ['id' => 3]);
        $inventoryLevel3
            ->setWarehouse($externalNotPreferableWarehouse)
            ->setInventoryQty(3);
        /** @var InventoryLevel $inventoryLevel4 */
        $inventoryLevel4 = $this->getEntity(InventoryLevel::class, ['id' => 4]);
        $inventoryLevel4
            ->setWarehouse($externalPreferableWarehouse)
            ->setInventoryQty(3);

        $inventoryItem1
            ->addInventoryLevel($inventoryLevel1);
        $inventoryItem3
            ->addInventoryLevel($inventoryLevel3)
            ->addInventoryLevel($inventoryLevel4);
        $inventoryItem4
            ->addInventoryLevel($inventoryLevel4);

        $orderItem1 = new OrderItem();
        $orderItem1
            ->setProduct($product1)
            ->setQuantity(2);
        $orderItem2 = new OrderItem();
        $orderItem2
            ->setProduct($product1)
            ->setQuantity(2);
        $orderItem3 = new OrderItem();
        $orderItem3
            ->setProduct($product2)
            ->setQuantity(2);
        $orderItem4 = new OrderItem();
        $orderItem4
            ->setProduct($product3)
            ->setQuantity(2);
        $orderItem5 = new OrderItem();
        $orderItem5
            ->setProduct($product4)
            ->setQuantity(2);
        $order = new Order();
        $order
            ->addItem($orderItem1)
            ->addItem($orderItem2)
            ->addItem($orderItem3)
            ->addItem($orderItem4)
            ->addItem($orderItem5);

        $salesChannelMock = $this->createMock(SalesChannel::class);
        $salesChannelGroupMock = $this->createMock(SalesChannelGroup::class);
        $salesChannelMock->expects(self::atLeastOnce())
            ->method('getGroup')
            ->willReturn($salesChannelGroupMock);
        $order->setSalesChannel($salesChannelMock);

        return [
            [
                'order' => $order,
                'warehouses' => new ArrayCollection([
                    $defaultWarehouse,
                    $externalNotPreferableWarehouse,
                    $externalPreferableWarehouse
                ]),
                'warehouseGroup' => $warehouseGroup,
                'expectedResult' => [
                    new OrderWarehouseResult([
                        OrderWarehouseResult::WAREHOUSE_FIELD => $defaultWarehouse,
                        OrderWarehouseResult::ORDER_ITEMS_FIELD => new ArrayCollection([$orderItem1])
                    ]),
                    new OrderWarehouseResult([
                        OrderWarehouseResult::WAREHOUSE_FIELD => $externalNotPreferableWarehouse,
                        OrderWarehouseResult::ORDER_ITEMS_FIELD => new ArrayCollection([$orderItem4])
                    ]),
                    new OrderWarehouseResult([
                        OrderWarehouseResult::WAREHOUSE_FIELD => $externalPreferableWarehouse,
                        OrderWarehouseResult::ORDER_ITEMS_FIELD => new ArrayCollection([$orderItem5])
                    ])
                ]
            ]
        ];
    }
}