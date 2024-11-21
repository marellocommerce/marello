<?php

namespace Marello\Bundle\OrderBundle\Provider\OrderItem;

interface OrderItemFormChangesProviderInterface
{
    /**
     * @param $rowId
     * @param $productId
     * @return string
     */
    public function getRowIdentifier($rowId, $productId): string;
}
