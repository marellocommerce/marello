<?php

namespace Marello\Bundle\ShippingBundle\Validator\Constraints;

use Marello\Bundle\ShippingBundle\Entity\ShippingMethodTypeConfig;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class EnabledTypeConfigsValidationGroupValidator extends ConstraintValidator
{
    /**
     * @param array $value
     * @param EnabledTypeConfigsValidationGroup|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!is_array($value) && !($value instanceof \Traversable && $value instanceof \Countable)) {
            throw new UnexpectedTypeException($value, 'array or Traversable and Countable');
        }

        $enabledRules = [];
        foreach ($value as $config) {
            if (!$config instanceof ShippingMethodTypeConfig) {
                throw new UnexpectedTypeException(
                    $config,
                    ShippingMethodTypeConfig::class
                );
            }
            if ($config->isEnabled()) {
                $enabledRules[] = $config;
            }
        }

        $count = count($enabledRules);

        if ($count < $constraint->min) {
            if ($this->context instanceof ExecutionContextInterface) {
                $builder = $this->context->buildViolation($constraint->message);
            } else {
                $builder = $this->buildViolation($constraint->message);
            }
            $builder
                ->setParameter('{{ count }}', $count)
                ->setParameter('{{ limit }}', $constraint->min)
                ->setPlural((int)$constraint->min)
                ->atPath('configurations')
                ->addViolation();
        }
    }
}
