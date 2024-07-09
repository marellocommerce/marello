<?php

namespace Marello\Bundle\OrderBundle\EventListener\Doctrine;

use Doctrine\ORM\Event\PrePersistEventArgs;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Entity\OrderItem;

class OrderItemsDiscountListener
{
    public function prePersist(Order $order, PrePersistEventArgs $args)
    {
        $orderDiscount = $order->getDiscountAmount();
        $total = (float)($order->getSubtotal() + $order->getTotalTax());

        if (0.00 === $total) {
            return;
        }

        /** @var OrderItem $item */
        foreach ($order->getItems() as $item) {
            $percent = $item->getPrice() * $item->getQuantity() / $total;
            $itemDiscount = $orderDiscount * $percent;
            $item->setDiscountAmount(round($itemDiscount, 4));
        }
    }
}
