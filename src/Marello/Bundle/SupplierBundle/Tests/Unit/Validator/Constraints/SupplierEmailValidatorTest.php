<?php

namespace Marello\Bundle\SupplierBundle\Tests\Unit\Validator\Constraints;

use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Marello\Bundle\SupplierBundle\Validator\Constraints\SupplierEmail;
use Marello\Bundle\SupplierBundle\Validator\Constraints\SupplierEmailValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\LogicException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class SupplierEmailValidatorTest extends TestCase
{
    protected SupplierEmailValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new SupplierEmailValidator();
    }

    public function testValidateWhenWrongConstraint()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('test value', $this->createMock(Constraint::class));
    }

    public function testValidateWhenWrongValue()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(sprintf(
            'The value must be instance of %s, got string',
            Supplier::class
        ));
        $this->validator->validate('test value', $this->createMock(SupplierEmail::class));
    }

    public function testValidateWhenWrongPoSendBy()
    {
        $value = new Supplier();
        $value->setPoSendBy(Supplier::SEND_PO_MANUALLY);
        $constraint = new SupplierEmail();
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())
            ->method('buildViolation');
        $this->validator->initialize($context);
        $this->validator->validate($value, $constraint);
    }

    public function testValidateWhenNoValidationError()
    {
        $value = new Supplier();
        $value->setPoSendBy(Supplier::SEND_PO_BY_EMAIL);
        $value->setEmail('example@gmail.com');
        $constraint = new SupplierEmail();
        $context = $this->createMock(ExecutionContextInterface::class);
        $context->expects($this->never())
            ->method('buildViolation');
        $this->validator->initialize($context);
        $this->validator->validate($value, $constraint);
    }

    public function testValidateWhen()
    {
        $value = new Supplier();
        $value->setPoSendBy(Supplier::SEND_PO_BY_EMAIL);
        $constraint = new SupplierEmail();
        $context = $this->createMock(ExecutionContextInterface::class);
        $violation = $this->createMock(ConstraintViolationBuilderInterface::class);
        $violation->expects($this->once())
            ->method('atPath')
            ->with('email')
            ->willReturnSelf();
        $violation->expects($this->once())
            ->method('addViolation');
        $context->expects($this->once())
            ->method('buildViolation')
            ->with($constraint->message)
            ->willReturn($violation);
        $this->validator->initialize($context);
        $this->validator->validate($value, $constraint);
    }
}
