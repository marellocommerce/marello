<?php

namespace Marello\Bundle\InvoiceBundle\Provider;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\InvoiceBundle\Entity\Invoice;
use Marello\Bundle\InvoiceBundle\Entity\AbstractInvoice;

class InvoicePaidAmountProvider
{
    /** @var DoctrineHelper $doctrineHelper */
    private $doctrineHelper;

    /**
     * @param AbstractInvoice $entity
     * @return float
     */
    public function getPaidAmount(AbstractInvoice $entity): float
    {
        $amount = 0.0;
        foreach ($entity->getPayments() as $payment) {
            $amount += (float)$payment->getTotalPaid();
        }

        return $this->formatAmount($amount);
    }

    /**
     * @param object $entity
     * @return float|null
     */
    public function getPaidAmountForEntity(object $entity): ?float
    {
        if ($entity instanceof Order) {
            $repo = $this->doctrineHelper->getEntityRepositoryForClass(AbstractInvoice::class);
            $invoices = $repo->findBy([
                'order' => $entity->getId(),
                'invoiceType' => Invoice::INVOICE_TYPE
            ]);

            $totalPaidAmount = 0.0;
            foreach ($invoices as $invoice) {
                $totalPaidAmount += $this->getPaidAmount($invoice);
            }

            return $this->formatAmount($totalPaidAmount);
        }

        if ($entity instanceof AbstractInvoice) {
            return $this->getPaidAmount($entity);
        }

        return null;
    }

    /**
     * @param object $entity
     * @return bool
     */
    public function isInvoiceAvailable(object $entity): bool
    {
        $isInvoiceAvailable = false;
        if ($entity instanceof Order) {
            $repo = $this->doctrineHelper->getEntityRepositoryForClass(AbstractInvoice::class);
            $invoices = $repo->findBy([
                'order' => $entity->getId(),
                'invoiceType' => Invoice::INVOICE_TYPE
            ]);
            if (!empty($invoices)) {
                $isInvoiceAvailable = true;
            }
        }

        if ($entity instanceof AbstractInvoice) {
            $isInvoiceAvailable = true;
        }

        return $isInvoiceAvailable;
    }

    /**
     * @param $amount
     * @return float
     */
    protected function formatAmount($amount): float
    {
        return number_format($amount, 4);
    }

    /**
     * @param DoctrineHelper $doctrineHelper
     * @return void
     */
    public function setDoctrineHelper(DoctrineHelper $doctrineHelper): void
    {
        $this->doctrineHelper = $doctrineHelper;
    }
}
