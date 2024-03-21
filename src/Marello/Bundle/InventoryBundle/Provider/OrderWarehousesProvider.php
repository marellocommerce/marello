<?php

namespace Marello\Bundle\InventoryBundle\Provider;

use Doctrine\Common\Collections\Collection;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\InventoryBundle\Entity\Allocation;
use Marello\Bundle\InventoryBundle\Strategy\WFA\WFAStrategiesRegistry;
use Marello\Bundle\InventoryBundle\Strategy\WFA\Quantity\QuantityWFAStrategy;

class OrderWarehousesProvider implements OrderWarehousesProviderInterface
{
    /** @var bool $estimation */
    private $estimation = false;

    /**
     * @var WFAStrategiesRegistry
     */
    protected $strategiesRegistry;

    /**
     * @var Collection|OrderItem[]
     */
    private $items;

    /**
     * @param WFAStrategiesRegistry $strategiesRegistry
     */
    public function __construct(WFAStrategiesRegistry $strategiesRegistry)
    {
        $this->strategiesRegistry = $strategiesRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getWarehousesForOrder(Order $order, Allocation $allocation = null): array
    {
        $results = [];
        $strategy = $this->strategiesRegistry->getStrategy(QuantityWFAStrategy::IDENTIFIER);
        $results = $strategy->getWarehouseResults($order, $allocation, $results, $this->items);

        if (count($results) > 0) {
            return $results;
        }

        return [];
    }

    /**
     * @param $items Collection|OrderItem[]
     * @return void
     */
    public function setOrderItemsForAllocation(Collection $items = null)
    {
        $this->items = $items;
    }
}
