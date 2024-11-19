<?php

namespace Marello\Bundle\ProductBundle\Provider;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\LayoutBundle\Context\FormChangeContextInterface;
use Marello\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Marello\Bundle\OrderBundle\Provider\OrderItem\AbstractOrderItemFormChangesProvider;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

class OrderItemProductUnitProvider extends AbstractOrderItemFormChangesProvider
{
    public function __construct(
        protected DoctrineHelper $doctrineHelper,
        protected AclHelper $aclHelper
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function processFormChanges(FormChangeContextInterface $context)
    {
        $submittedData = $context->getSubmittedData();
        $order = $context->getForm()->getData();
        if ($order instanceof Order) {
            $salesChannel = $order->getSalesChannel();
        } else {
            return;
        }
        $productIds = [];
        $data = [];

        foreach ($submittedData[self::ITEMS_FIELD] as $rowId => $item) {
            /** @var Product[] $products */
            $products = $this->getRepository()
                ->findBySalesChannel($salesChannel->getId(), [(int)$item['product']], $this->aclHelper);
            foreach ($products as $product) {
                $inventoryItem = $product->getInventoryItem();
                $unit = $inventoryItem->getProductUnit();
                if ($unit) {
                    $data[$this->getRowIdentifier($rowId, $item['product'])] = [
                        'unit' => $unit->getName()
                    ];
                }
            }
            if (!empty($data)) {
                $result = $context->getResult();
                $result[self::ITEMS_FIELD]['product_unit'] = $data;
                $context->setResult($result);
            }
        }
    }

    /**
     * @return ProductRepository
     */
    protected function getRepository()
    {
        return $this->doctrineHelper->getEntityRepositoryForClass(Product::class);
    }
}
