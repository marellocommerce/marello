<?php

namespace Marello\Bundle\OrderBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;

use Oro\Bundle\AddressBundle\Entity\Address;
use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\SalesBundle\Entity\SalesChannel;

class OrderTest extends TestCase
{
    use EntityTestCaseTrait;

    public function testAccessors()
    {
        $this->assertPropertyAccessors(new Order(), [
            ['id', 42],
            ['orderNumber', 'some string'],
            ['orderReference', 'some string'],
            ['invoiceReference', 'some string'],
            ['subtotal', 42],
            ['totalTax', 42],
            ['grandTotal', 42],
            ['currency', 'some string'],
            ['paymentMethod', 'some string'],
            ['shippingAmountInclTax', 'some string'],
            ['shippingAmountExclTax', 'some string'],
            ['shippingMethod', 3.1415926],
            ['discountAmount', 'some string'],
            ['discountPercent', 3.1415926],
            ['couponCode', 'some string'],
            ['customer', new Customer()],
            ['billingAddress', new Address()],
            ['shippingAddress', new Address()],
            ['invoicedAt', new \DateTime()],
            ['salesChannel', new SalesChannel()],
            ['salesChannelName', 'some string'],
            ['organization', new Organization()],
            ['localization', new Localization()],
            ['createdAt', new \DateTime()],
            ['updatedAt', new \DateTime()]
        ]);
        $this->assertPropertyCollections(new Order(), [
            ['items', new OrderItem()],
        ]);
    }
}
