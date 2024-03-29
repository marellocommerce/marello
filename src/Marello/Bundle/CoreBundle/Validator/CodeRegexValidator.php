<?php

namespace Marello\Bundle\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

use Marello\Bundle\CoreBundle\Validator\Constraints\CodeRegex;

/**
 * Regex validator for 'code' attribute
 */
class CodeRegexValidator extends ConstraintValidator
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @param string $pattern
     */
    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CodeRegex) {
            throw new UnexpectedTypeException($constraint, CodeRegex::class);
        }

        if (!$value) {
            return;
        }

        $validator = $this->context->getValidator();
        $violations = $validator->validate(
            $value,
            new Regex(['pattern' => $this->pattern])
        );

        if ($violations->count()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
