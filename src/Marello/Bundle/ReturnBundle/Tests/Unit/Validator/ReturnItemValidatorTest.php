<?php

namespace Marello\Bundle\ReturnBundle\Tests\Unit\Validator;

use Marello\Bundle\ProductBundle\Provider\ProductTaxCodeProvider;
use PHPUnit\Framework\TestCase;

use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Bundle\ReturnBundle\Entity\ReturnItem;
use Marello\Bundle\ReturnBundle\Util\ReturnHelper;
use Marello\Bundle\ReturnBundle\Validator\ReturnItemValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Marello\Bundle\ReturnBundle\Validator\Constraints\ReturnItemConstraint;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class ReturnItemValidatorTest extends TestCase
{
    /** @var ReturnItemValidator */
    protected $validator;

    /** @var ConstraintViolationBuilderInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $builder;

    /** @var ReturnHelper|\PHPUnit\Framework\MockObject\MockObject */
    protected $returnHelper;

    protected function setUp(): void
    {
        $this->builder = $this
            ->getMockForAbstractClass(ConstraintViolationBuilderInterface::class);

        $this->builder->expects($this->any())
            ->method('atPath')
            ->will($this->returnValue($this->builder));

        $context = $this->getMockForAbstractClass(ExecutionContextInterface::class);

        $context->expects($this->any())
            ->method('buildViolation')
            ->will($this->returnValue($this->builder));

        $this->returnHelper = $this->createMock(ReturnHelper::class);

        $this->validator = new ReturnItemValidator($this->returnHelper);
        $this->validator->initialize($context);
    }

    /**
     * @param array $distribution
     * @param int   $total
     *
     * @return ReturnItem
     */
    protected function getItem($distribution = [10], $total = 10)
    {
        $orderItem = new OrderItem();
        $orderItem->setQuantity($total);

        $testedItem = null;

        foreach ($distribution as $q) {
            $returnItem = new ReturnItem($orderItem);
            $returnItem->setQuantity($q);

            if ($testedItem) {
                $orderItem->getReturnItems()->add($returnItem);
            } else {
                $testedItem = $returnItem;
            }
        }

        return $testedItem;
    }

    public function validateDataProvider()
    {
        return [
            'VALID: One Return item with same quantity as shipped'      => [$this->getItem(), true, 10],
            'VALID: One return item with quantity less than shipped'    => [$this->getItem([5]), true, 10],
            'VALID: Two return items with same quantity as shipped'     => [$this->getItem([5, 5]), true, 10],
            'VALID: Two return items with lower quantity as shipped'    => [$this->getItem([2, 3]), true, 5],
            'INVALID: One return item with quantity more than shipped'  => [$this->getItem([14]), false, 10],
            'INVALID: Two return items with quantity more than shipped' => [$this->getItem([7, 6]), false, 7]
        ];
    }

    /**
     * @dataProvider validateDataProvider
     *
     * @param ReturnItem $item
     * @param bool       $valid
     */
    public function testValidate(ReturnItem $item, $valid, $shippedQty)
    {
        if ($valid) {
            $this->builder
                ->expects($this->never())
                ->method('addViolation');
        } else {
            $this->builder
                ->expects($this->once())
                ->method('addViolation');
        }
        $this->returnHelper->expects(static::atLeastOnce())
            ->method('getOrderItemShippedQuantity')
            ->willReturn($shippedQty);

        $this->validator->validate($item, new ReturnItemConstraint());
    }
}
