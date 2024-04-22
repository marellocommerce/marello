<?php

namespace Marello\Bundle\ProductBundle\Entity;

use Marello\Bundle\InventoryBundle\Model\InventoryItemAwareInterface;

interface ProductInterface extends InventoryItemAwareInterface
{
    public function getSku(): string;

    public function setSku(string $sku);
}
