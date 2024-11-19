<?php

namespace Marello\Bundle\ProductBundle\Provider;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\LayoutBundle\Context\FormChangeContextInterface;
use Marello\Bundle\OrderBundle\Provider\OrderItem\AbstractOrderItemFormChangesProvider;

class ProductTaxCodeProvider extends AbstractOrderItemFormChangesProvider
{
    public function __construct(
        protected ManagerRegistry $registry,
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

        if (!array_key_exists(self::ITEMS_FIELD, $submittedData)) {
            return;
        }

        $data = [];
        foreach ($submittedData[self::ITEMS_FIELD] as $rowId => $item) {
            /** @var Product[] $products */
            $products = $this->getRepository()
                ->findBySalesChannel($salesChannel->getId(), [(int)$item['product']], $this->aclHelper);
            foreach ($products as $product) {
                $taxCode = $product->getSalesChannelTaxCode($salesChannel) ? : $product->getTaxCode();
                if ($taxCode) {
                    $data[$this->getRowIdentifier($rowId, $item['product'])] = [
                        'id' => $taxCode->getId(),
                        'code' => $taxCode->getCode()
                    ];
                }
            }
        }

        if (!empty($data)) {
            $result = $context->getResult();
            $result[self::ITEMS_FIELD]['tax_code'] = $data;
            $context->setResult($result);
        }
    }

    /**
     * @return \Doctrine\Persistence\ObjectRepository
     */
    protected function getRepository()
    {
        return $this->registry->getManagerForClass(Product::class)->getRepository(Product::class);
    }
}
