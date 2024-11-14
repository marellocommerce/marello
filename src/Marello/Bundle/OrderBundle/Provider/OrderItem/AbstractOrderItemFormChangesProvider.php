<?php

namespace Marello\Bundle\OrderBundle\Provider\OrderItem;

use Marello\Bundle\LayoutBundle\Provider\FormChangesProviderInterface;

abstract class AbstractOrderItemFormChangesProvider implements
    FormChangesProviderInterface,
    OrderItemFormChangesProviderInterface
{
    const IDENTIFIER_PREFIX = 'product-id-';
    const ITEMS_FIELD = 'items';
    const CHANNEL_FIELD = 'salesChannel';

    /**
     * Get product ids from the submitted data
     * @param array $submittedData
     * @return array
     */
    public function getRowItemIdentifiersFromSubmittedData(array $submittedData)
    {
        $rowIds = [];
        foreach ($submittedData[self::ITEMS_FIELD] as $rowId => $item) {
            $rowIds[] = $this->getRowIdentifier($rowId, (int)$item['product']);
        }

        return $rowIds;
    }

    /**
     * @param $rowId
     * @param $productId
     * @return string
     */
    public function getRowIdentifier($rowId, $productId): string
    {
        return sprintf('%s%s-%s', self::IDENTIFIER_PREFIX, $rowId, $productId);
    }
}
