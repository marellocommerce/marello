<?php

namespace Marello\Bundle\InvoiceBundle\Tests\Functional\Provider;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use Marello\Bundle\InvoiceBundle\Tests\Functional\DataFixtures\LoadInvoiceData;
use Marello\Bundle\ProductBundle\Tests\Functional\DataFixtures\LoadProductData;

class InvoicePaidAmountProviderTest extends WebTestCase
{
    const PROVIDER_SERVICE_ID = 'marello_invoice.provider.invoice_paid_amount';

    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());
        $this->client->useHashNavigation(true);

        $this->loadFixtures([
            LoadInvoiceData::class
        ]);
        $this->paidAmountProvider = $this->client->getContainer()->get(self::PROVIDER_SERVICE_ID);
    }

    public function testIsInvoiceAvailable()
    {
        $order = $this->getReference('marello_order_0');
        self::assertTrue($this->paidAmountProvider->isInvoiceAvailable($order));
    }

    public function testGetInvoiceNotAvailable()
    {
        $product = $this->getReference(LoadProductData::PRODUCT_1_REF);
        self::assertFalse($this->paidAmountProvider->isInvoiceAvailable($product));
    }

    public function testGetPaidAmountForEntity()
    {
        $order = $this->getReference('marello_order_0');
        $invoice = $this->getReference('marello_invoice_0');
        $paymentHandler = $this->client->getContainer()->get('marello_payment.action.handler.add_payment');
        $resultPaymentHandler = $paymentHandler
            ->handleAction(
                $invoice,
                'payment_term_1',
                new \DateTime('now', new \DateTimeZone('UTC')),
                'ref1',
                'detail1',
                280.000
            );
        self::assertEquals(
            $resultPaymentHandler,
            [
                'type' => 'success',
                'message' => 'marello.payment.message.add_payment.success'
            ]
        );

        self::assertEquals($order->getGrandTotal(), $this->paidAmountProvider->getPaidAmountForEntity($order));
    }

    public function testPaidAmountNotTheSame()
    {
        $order = $this->getReference('marello_order_1');
        $invoice = $this->getReference('marello_invoice_1');
        $paymentHandler = $this->client->getContainer()->get('marello_payment.action.handler.add_payment');
        $resultPaymentHandler = $paymentHandler
            ->handleAction(
                $invoice,
                'payment_term_1',
                new \DateTime('now', new \DateTimeZone('UTC')),
                'ref1',
                'detail1',
                140.000
            );
        self::assertEquals(
            $resultPaymentHandler,
            [
                'type' => 'success',
                'message' => 'marello.payment.message.add_payment.success'
            ]
        );
        self::assertNotEquals($order->getGrandTotal(), $this->paidAmountProvider->getPaidAmountForEntity($invoice));
    }
}
