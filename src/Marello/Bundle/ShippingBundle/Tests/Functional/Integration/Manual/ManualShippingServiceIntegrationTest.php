<?php

namespace Marello\Bundle\ShippingBundle\Tests\Functional\Integration\Manual;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\InventoryBundle\Entity\Allocation;
use Marello\Bundle\ReturnBundle\Entity\ReturnEntity;
use Marello\Bundle\ShippingBundle\Entity\Shipment;
use Marello\Bundle\ShippingBundle\Integration\Manual\ManualShippingServiceDataFactory;
use Marello\Bundle\ShippingBundle\Integration\Manual\ManualShippingServiceIntegration;
use Marello\Bundle\OrderBundle\Tests\Functional\DataFixtures\LoadOrderData;
use Marello\Bundle\InventoryBundle\Tests\Functional\DataFixtures\LoadAllocationData;

class ManualShippingServiceIntegrationTest extends WebTestCase
{
    /** @var ManualShippingServiceIntegration */
    protected $integration;

    /** @var ManualShippingServiceDataFactory */
    protected $dataFactory;

    protected function setUp(): void
    {
        $this->initClient();

        $this->loadFixtures(
            [
                LoadOrderData::class,
                LoadAllocationData::class
            ]
        );

        $this->dataFactory = $this->getContainer()->get('marello_shipping.integration.manual.service_data_factory');
        $this->integration = $this->getContainer()->get('marello_shipping.integration.manual.service_integration');
    }

    public function testIntegrationOrder()
    {
        /** @var Order $order */
        $order = $this->getReference('marello_order_1');
        $allocation = $this->getContainer()->get('doctrine')
            ->getManagerForClass(Allocation::class)
            ->getRepository(Allocation::class)
            ->findOneBy([
                'order' => $order->getId()
            ]);

        $shippingDataProvider = $this->client
            ->getContainer()
            ->get('marello_order.shipping.integration.service_data_provider');
        $shippingDataProvider = $shippingDataProvider
            ->setEntity($allocation)
            ->setWarehouse($this->getReference(LoadOrderData::DEFAULT_WAREHOUSE_REF));

        $data = $this->dataFactory->createData($shippingDataProvider);

        $integration = $this->client->getContainer()->get('marello_shipping.integration.manual.service_integration');
        $shipment = $integration->createShipment($allocation, $data);

        $this->assertInstanceOf(Shipment::class, $shipment);
    }
    
    public function testIntegrationReturn()
    {
        /** @var ReturnEntity $return */
        $return = $this->getReference('return1');

        $shippingDataProvider = $this->client
            ->getContainer()
            ->get('marello_return.shipping.integration.service_data_provider');
        $shippingDataProvider = $shippingDataProvider
            ->setEntity($return)
            ->setWarehouse($this->getReference(LoadOrderData::DEFAULT_WAREHOUSE_REF));

        $data = $this->dataFactory->createData($shippingDataProvider);

        $integration = $this->client->getContainer()->get('marello_shipping.integration.manual.service_integration');
        $shipment = $integration->createShipment($return, $data);

        $this->assertInstanceOf(Shipment::class, $shipment);
    }
}
