<?php

namespace Marello\Bundle\InventoryBundle\Provider\Allocation;

use Doctrine\Common\Collections\Collection;

interface AllocationItemFilterInterface
{
    public function getFilteredItems(Collection $items): Collection;

    public function getUseDifferentSalesChannel(Collection $items): bool;
}
