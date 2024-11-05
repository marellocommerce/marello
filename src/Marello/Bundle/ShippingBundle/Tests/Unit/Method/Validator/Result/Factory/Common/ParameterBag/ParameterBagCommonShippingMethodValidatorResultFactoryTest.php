<?php

namespace Marello\Bundle\ShippingBundle\Tests\Unit\Method\Validator\Result\Factory\Common\ParameterBag;

use Marello\Bundle\ShippingBundle\Method\Validator\Result\Error;
use Marello\Bundle\ShippingBundle\Method\Validator\Result\Factory;
use Marello\Bundle\ShippingBundle\Method\Validator\Result\ParameterBag\ParameterBagShippingMethodValidatorResult;

class ParameterBagCommonShippingMethodValidatorResultFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Factory\Common\ParameterBag\ParameterBagCommonShippingMethodValidatorResultFactory
     */
    private $factory;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        $this->factory = new Factory\Common\ParameterBag\ParameterBagCommonShippingMethodValidatorResultFactory();
    }

    public function testCreateSuccessResult()
    {
        // TODO:: fix me
        return;
        $collection = new Error\Collection\Doctrine\DoctrineShippingMethodValidatorResultErrorCollection();
        static::assertEquals(new ParameterBagShippingMethodValidatorResult(
            [
                'errors' => $collection->toArray(),
            ]
        ), $this->factory->createSuccessResult());
    }

    public function testCreateErrorResult()
    {
        // TODO:: fix me
        return;
        /** @var Error\Collection\ShippingMethodValidatorResultErrorCollectionInterface $errors */
        $errors = $this->createMock(Error\Collection\ShippingMethodValidatorResultErrorCollectionInterface::class);
        static::assertEquals(new ParameterBagShippingMethodValidatorResult(
            [
                'errors' => $errors,
            ]
        ), $this->factory->createErrorResult($errors->toArray()));
    }
}
