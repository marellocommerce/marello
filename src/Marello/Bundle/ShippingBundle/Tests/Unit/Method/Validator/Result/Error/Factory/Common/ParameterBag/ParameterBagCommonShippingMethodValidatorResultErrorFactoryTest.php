<?php

namespace Marello\Bundle\ShippingBundle\Tests\Unit\Method\Validator\Result\Error\Factory\Common\ParameterBag;

use Marello\Bundle\ShippingBundle\Method\Validator\Result\Error\Factory;
use Marello\Bundle\ShippingBundle\Method\Validator\Result\Error\ParameterBag\ParameterBagShippingMethodValidatorResultError;

class ParameterBagCommonShippingMethodValidatorResultErrorFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateError()
    {
        $message = 'Error message';

        $factory = new Factory\Common\ParameterBag\ParameterBagCommonShippingMethodValidatorResultErrorFactory();

        static::assertEquals(new ParameterBagShippingMethodValidatorResultError(
            [
                ParameterBagShippingMethodValidatorResultError::FIELD_MESSAGE => $message,
            ]
        ), $factory->createError($message));
    }
}
