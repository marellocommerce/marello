<?php

namespace Marello\Bundle\UPSBundle\Validator\Constraints;

use Marello\Bundle\UPSBundle\Entity\UPSSettings;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class CountryShippingServicesValidator extends ConstraintValidator
{
    const ALIAS = 'marello_ups_country_shipping_services_validator';

    /**
     * @internal
     */
    const VIOLATION_PATH = 'applicableShippingServices';

    /**
     * @param UPSSettings                                 $value
     * @param Constraint|CountryShippingServicesConstraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof UPSSettings) {
            return;
        }

        $settingsCountry = $value->getUpsCountry();

        if (!$settingsCountry) {
            return;
        }

        /** @var ExecutionContextInterface $context */
        $context = $this->context;

        foreach ($value->getApplicableShippingServices() as $applicableShippingService) {
            $shippingServiceCountry = $applicableShippingService->getCountry();

            if ($shippingServiceCountry !== $settingsCountry) {
                $context
                    ->buildViolation($constraint->message, [
                        '%shipping_service%' => (string)$applicableShippingService,
                        '%settings_country%' => (string)$settingsCountry,
                        '%shipping_service_country%' => (string)$shippingServiceCountry,
                    ])
                    ->atPath(self::VIOLATION_PATH)
                    ->addViolation();
            }
        }
    }
}
