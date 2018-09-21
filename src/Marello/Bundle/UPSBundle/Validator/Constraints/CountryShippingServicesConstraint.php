<?php

namespace Marello\Bundle\UPSBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CountryShippingServicesConstraint extends Constraint
{
    /**
     * @var string
     */
    public $message = 'marello.ups.settings.shipping_service.wrong_country.message';

    /**
     * {@inheritDoc}
     */
    public function validatedBy()
    {
        return CountryShippingServicesValidator::ALIAS;
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
