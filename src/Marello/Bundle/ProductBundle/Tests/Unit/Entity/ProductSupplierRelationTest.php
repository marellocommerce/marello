<?php

namespace Marello\Bundle\ProductBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;

use Oro\Component\Testing\Unit\EntityTestCaseTrait;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Marello\Bundle\ProductBundle\Entity\ProductSupplierRelation;

class ProductSupplierRelationTest extends TestCase
{
    use EntityTestCaseTrait;

    public function testAccessors()
    {
        $this->assertPropertyAccessors(new ProductSupplierRelation(), [
            ['id', 42],
            ['product', new Product(), false],
            ['supplier', new Supplier(), false],
            ['quantityOfUnit', 42, false],
            ['priority', 42, false],
            ['cost', 0.00],
            ['canDropship', true]
        ]);
    }
}
