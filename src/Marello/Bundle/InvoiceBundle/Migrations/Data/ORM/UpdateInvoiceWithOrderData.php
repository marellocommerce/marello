<?php

namespace Marello\Bundle\InvoiceBundle\Migrations\Data\ORM;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Marello\Bundle\InvoiceBundle\Entity\AbstractInvoice;
use Marello\Bundle\InvoiceBundle\Entity\Invoice;

class UpdateInvoiceWithOrderData extends AbstractFixture
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->updateCurrentInvoices();

        $this->manager->flush();
    }

    /**
     * update current InvoiceItem with additional properties poNumber and discountAmount
     */
    public function updateCurrentInvoices()
    {
        $invoices = $this->manager
            ->getRepository(AbstractInvoice::class)
            ->findBy(['invoiceType' => Invoice::INVOICE_TYPE]);

        /** @var AbstractInvoice $invoice*/
        foreach ($invoices as $invoice) {
            if ($order = $invoice->getOrder()) {
                $invoice->setDiscountAmount($order->getDiscountAmount());
                $invoice->setPoNumber($order->getPoNumber());
                $this->manager->persist($invoice);
            }
        }
    }
}
