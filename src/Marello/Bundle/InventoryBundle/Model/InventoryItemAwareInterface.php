<?php

namespace Marello\Bundle\InventoryBundle\Model;

use Marello\Bundle\InventoryBundle\Entity\InventoryItem;

interface InventoryItemAwareInterface
{
    public function getInventoryItem(): ?InventoryItem;
}
