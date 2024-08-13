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

        $isExist = $this->entityManager->getRepository(Customer::class)->isExistCustomerByEmailAndOrganization($value);
        if ($isExist) {
            $this->context->buildViolation($constraint->message)
                ->atPath('email')
                ->setInvalidValue($value)
                ->addViolation();
        }
    }
}
