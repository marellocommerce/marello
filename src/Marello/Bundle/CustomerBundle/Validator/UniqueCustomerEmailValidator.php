<?php

namespace Marello\Bundle\CustomerBundle\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueCustomerEmailValidator extends ConstraintValidator
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof Customer) {
            return;
        }

        if (!$value->getEmail()) {
            return;
        }

        /** @var array|Customer $customer */
        $customer = $this->entityManager->getRepository(Customer::class)->findCustomerByEmailAndOrganization($value);
        $existingCustomerCount = 0;
        if ($value->getId()) {
            $existingCustomerCount = 1;
        }

        if (is_array($customer) && count($customer) > $existingCustomerCount) {
            $this->failValidation($constraint, $value);
        }
    }

    protected function failValidation($constraint, $value)
    {
        $this->context->buildViolation($constraint->message)
            ->atPath('email')
            ->setInvalidValue($value)
            ->addViolation();
    }
}
