<?php

namespace Marello\Bundle\ShippingBundle\Method\Validator\Result\ParameterBag;

use Marello\Bundle\ShippingBundle\Method\Validator\Result\Factory\Common;
use Marello\Bundle\ShippingBundle\Method\Validator\Result\ShippingMethodValidatorResultInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ParameterBagShippingMethodValidatorResult extends ParameterBag implements ShippingMethodValidatorResultInterface
{
    const FIELD_ERRORS = 'errors';

    /**
     * {@inheritDoc}
     */
    public function createCommonFactory()
    {
        return new Common\ParameterBag\ParameterBagCommonShippingMethodValidatorResultFactory();
    }

    /**
     * {@inheritDoc}
     */
    public function getErrors()
    {
        return $this->get(self::FIELD_ERRORS);
    }
}
