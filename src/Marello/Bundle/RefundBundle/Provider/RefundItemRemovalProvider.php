<?php

namespace Marello\Bundle\RefundBundle\Provider;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

use Marello\Bundle\RefundBundle\Entity\Refund;
use Marello\Bundle\RefundBundle\Entity\RefundItem;

class RefundItemRemovalProvider
{
    public function __construct(
        protected DoctrineHelper $doctrineHelper
    ) {
    }

    public function removeFromRefund(RefundItem $item)
    {
        $refund = $item->getRefund();
        if ((float)$item->getRefundAmount() === 0.00) {
            $refund->removeItem($item);
            $em = $this->doctrineHelper->getEntityManagerForClass(Refund::class);
            $em->persist($refund);
            $em->flush();
        }
    }
}
