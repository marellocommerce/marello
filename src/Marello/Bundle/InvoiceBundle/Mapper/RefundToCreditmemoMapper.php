<?php

namespace Marello\Bundle\InvoiceBundle\Mapper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Marello\Bundle\InvoiceBundle\Entity\Creditmemo;
use Marello\Bundle\InvoiceBundle\Entity\CreditmemoItem;
use Marello\Bundle\RefundBundle\Entity\Refund;
use Marello\Bundle\RefundBundle\Entity\RefundItem;

class RefundToCreditmemoMapper extends AbstractInvoiceMapper
{
    /**
     * {@inheritdoc}
     */
    public function map($sourceEntity)
    {
        if (!($sourceEntity instanceof Refund)) {
            throw new \InvalidArgumentException(
                sprintf('Wrong source entity "%s" provided to RefundToCreditmemoMapper', get_class($sourceEntity))
            );
        }

        /** @var Refund $sourceEntity */
        $creditmemo = new Creditmemo();
        $data = $this->getData($sourceEntity->getOrder(), Creditmemo::class);
        $data['order'] = $sourceEntity->getOrder();
        $data['items'] = $this->getItems($sourceEntity->getItems());
        $totalTax = 0.00;
        /** @var CreditmemoItem $item */
        foreach ($data['items'] as $item) {
            $totalTax += $item->getTax();
        }
        $data['subtotal'] = $sourceEntity->getRefundAmount();
        $data['totalTax'] = $totalTax;
        $data['grandTotal'] = $sourceEntity->getRefundAmount();
        $data['total_due'] = $sourceEntity->getRefundAmount();

        $data['shippingAmountExclTax'] = 0;
        $data['shippingAmountInclTax'] = 0;
        if ($data['invoicedAt'] === null) {
            $data['invoicedAt'] = new \DateTime('now', new \DateTimeZone('UTC'));
        }

        $this->assignData($creditmemo, $data);

        return $creditmemo;
    }

    /**
     * @param Collection $items
     * @return ArrayCollection
     */
    protected function getItems(Collection $items)
    {
        $refundItems = $items->toArray();
        $creditmemoItems = [];
        /** @var RefundItem $item */
        foreach ($refundItems as $item) {
            if (0.00 === (float)$item->getRefundAmount()) {
                continue;
            }
            $creditmemoItems[] = $this->mapItem($item);
        }

        return new ArrayCollection($creditmemoItems);
    }

    /**
     * @param RefundItem $refundItem
     * @return CreditmemoItem|null
     */
    protected function mapItem(RefundItem $refundItem)
    {
        $creditmemoItem = new CreditmemoItem();
        $creditmemoItemData['price'] = $refundItem->getRefundAmount();
        $creditmemoItemData['tax'] = $refundItem->getTaxTotal();
        $creditmemoItemData['rowTotalInclTax'] = $refundItem->getRefundAmount();
        $creditmemoItemData['rowTotalExclTax'] = ($refundItem->getRefundAmount() - $refundItem->getTaxTotal());
        $creditmemoItemData['quantity'] = $refundItem->getQuantity();
        $creditmemoItemData['productSku'] = 'N/A';
        $creditmemoItemData['productName'] = $refundItem->getName();

        $orderItem = $refundItem->getOrderItem();
        if ($orderItem) {
            $creditmemoItemData['productUnit'] = $orderItem->getProductUnit() ? $orderItem->getProductUnit()->getId() : null;
            $creditmemoItemData['productSku'] = $orderItem->getProductSku();
            $creditmemoItemData['productName'] = $orderItem->getProductName();
        }

        $this->assignData($creditmemoItem, $creditmemoItemData);

        return $creditmemoItem;
    }
}
