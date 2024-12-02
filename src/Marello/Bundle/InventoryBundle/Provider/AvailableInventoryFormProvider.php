<?php

namespace Marello\Bundle\InventoryBundle\Provider;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\LayoutBundle\Context\FormChangeContextInterface;
use Marello\Bundle\OrderBundle\Provider\OrderItem\AbstractOrderItemFormChangesProvider;

class AvailableInventoryFormProvider extends AbstractOrderItemFormChangesProvider
{
    const PRODUCT_FIELD = 'product';
    const INVENTORY_FIELD = 'inventory';

    /** @var AvailableInventoryProvider $availableInventoryProvider */
    protected $availableInventoryProvider;

    /**
     * {@inheritdoc}
     * @param AvailableInventoryProvider $availableInventoryProvider
     */
    public function __construct(AvailableInventoryProvider $availableInventoryProvider)
    {
        $this->availableInventoryProvider = $availableInventoryProvider;
    }

    /**
     * {@inheritdoc}
     * @param FormChangeContextInterface $context
     */
    public function processFormChanges(FormChangeContextInterface $context)
    {
        $submittedData = $context->getSubmittedData();
        $form = $context->getForm();
        $order = $form->getData();
        if ($order instanceof Order) {
            $salesChannel = $order->getSalesChannel();
        } else {
            return;
        }

        if (!array_key_exists(self::ITEMS_FIELD, $submittedData)) {
            return;
        }

        $data = [];
        foreach ($submittedData[self::ITEMS_FIELD] as $rowId => $item) {
            if (!array_key_exists(self::PRODUCT_FIELD, $item)) {
                continue;
            }
            $products = $this->availableInventoryProvider
                ->getProducts(
                    $salesChannel->getId(),
                    [(int)$item[self::PRODUCT_FIELD]]
                );

            if (0 === count($products)) {
                return;
            }
            $rowIdentifier = $this->getRowIdentifier($rowId, $item[self::PRODUCT_FIELD]);
            /** @var Product $product */
            foreach ($products as $product) {
                $availableInventory = $this->availableInventoryProvider->getAvailableInventory($product, $salesChannel);
                $data[$rowIdentifier]['value'] = $availableInventory;
            }
        }

        $result = $context->getResult();

        if (!empty($data)) {
            $result[self::ITEMS_FIELD][self::INVENTORY_FIELD] = $data;
        }

        $context->setResult($result);
    }
}
