<?php

namespace Marello\Bundle\InventoryBundle\Provider\Allocation;

use Doctrine\Common\Collections\Collection;

use Marello\Bundle\InventoryBundle\Provider\AllocationExclusionInterface;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\InventoryBundle\Entity\Allocation;
use Marello\Bundle\OrderBundle\Model\OrderItemTypeInterface;

class AllocationItemFilterProvider implements AllocationItemFilterInterface
{
    public function getFilteredItems(Collection $items): Collection
    {
        // option to filter orderItems for excluding items from allocating
        return $items->filter(function (OrderItem $item) {
            return !$item->isAllocationExclusion();
        });
    }

    public function getUseDifferentSalesChannel(Collection $items): bool
    {
        $useDifferentSalesChannel = false;
        foreach ($items as $specifiedItem) {
            if ($specifiedItem->getItemType() === OrderItemTypeInterface::OI_TYPE_DELIVERY) {
                $useDifferentSalesChannel = true;
            }
        }
        return $useDifferentSalesChannel;
    }

}
