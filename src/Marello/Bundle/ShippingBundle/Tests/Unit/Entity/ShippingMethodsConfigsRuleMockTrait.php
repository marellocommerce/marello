<?php

namespace Marello\Bundle\ShippingBundle\Tests\Unit\Entity;

use Marello\Bundle\ShippingBundle\Entity\ShippingMethodsConfigsRule;

/**
 * @method \PHPUnit\Framework\MockObject\MockObject createMock(string $originalClassName)
 */
trait ShippingMethodsConfigsRuleMockTrait
{
    /**
     * @return ShippingMethodsConfigsRule|\PHPUnit\Framework\MockObject\MockObject
     */
    private function createShippingMethodsConfigsRuleMock()
    {
        return $this->createMock(ShippingMethodsConfigsRule::class);
    }
}
