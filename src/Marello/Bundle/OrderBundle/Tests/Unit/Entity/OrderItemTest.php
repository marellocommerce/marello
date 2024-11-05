<?php

namespace Marello\Bundle\OrderBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;

use Oro\Component\Testing\Unit\EntityTestCaseTrait;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\ReturnBundle\Entity\ReturnItem;

class OrderItemTest extends TestCase
{
    use EntityTestCaseTrait;

    public function testAccessors()
    {
        $this->assertPropertyAccessors(new OrderItem(), [
            ['id', 42],
            ['product', new Product()],
            ['productName', 'some string'],
            ['productSku', 'some string'],
            ['variantHash', md5('some string'), false],
            ['order', new Order()],
            ['quantity', 42],
            ['price', 42],
            ['originalPriceInclTax', 42],
            ['originalPriceExclTax', 42],
            ['purchasePriceIncl', 42],
            ['tax', 42],
            ['taxPercent', 3.1415926],
            ['discountPercent', 3.1415926],
            ['discountAmount', 'some string'],
            ['rowTotalInclTax', 42],
            ['rowTotalExclTax', 42],
            ['taxCode', new TaxCode()],
            ['organization', new Organization()]
        ]);
        $this->assertPropertyCollections(new OrderItem(), [
            ['returnItems', new ReturnItem()],
        ]);
    }
}
