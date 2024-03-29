<?php

namespace Marello\Bundle\PaymentBundle\Context;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Implements PaymentContextInterface and provides the ability to save and manage parameters for payment
 */
class PaymentContext extends ParameterBag implements PaymentContextInterface
{
    const FIELD_COMPANY = 'company';
    const FIELD_CUSTOMER = 'customer';
    const FIELD_LINE_ITEMS = 'line_items';
    const FIELD_BILLING_ADDRESS = 'billing_address';
    const FIELD_SHIPPING_ADDRESS = 'payment_address';
    const FIELD_SHIPPING_ORIGIN = 'shipping_origin';
    const FIELD_PAYMENT_METHOD = 'payment_method';
    const FIELD_CURRENCY = 'currency';
    const FIELD_SUBTOTAL = 'subtotal';
    const FIELD_SOURCE_ENTITY = 'source_entity';
    const FIELD_SOURCE_ENTITY_ID = 'source_entity_id';
    const FIELD_TOTAL = 'total';

    /**
     * @param array $params
     */
    public function __construct(array $params)
    {
        parent::__construct($params);
    }
    /**
     * {@inheritDoc}
     */
    public function getCompany()
    {
        return $this->get(self::FIELD_COMPANY);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomer()
    {
        return $this->get(self::FIELD_CUSTOMER);
    }

    /**
     * {@inheritDoc}
     */
    public function getLineItems()
    {
        return $this->get(self::FIELD_LINE_ITEMS);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingAddress()
    {
        return $this->get(self::FIELD_BILLING_ADDRESS);
    }

    /**
     * {@inheritDoc}
     */
    public function getShippingAddress()
    {
        return $this->get(self::FIELD_SHIPPING_ADDRESS);
    }

    /**
     * {@inheritDoc}
     */
    public function getShippingOrigin()
    {
        return $this->get(self::FIELD_SHIPPING_ORIGIN);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentMethod()
    {
        return $this->get(self::FIELD_PAYMENT_METHOD);
    }

    /**
     * {@inheritDoc}
     */
    public function getCurrency()
    {
        return $this->get(self::FIELD_CURRENCY);
    }

    /**
     * {@inheritDoc}
     */
    public function getSubtotal()
    {
        return $this->get(self::FIELD_SUBTOTAL);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getSourceEntity()
    {
        return $this->get(self::FIELD_SOURCE_ENTITY);
    }

    /**
     * {@inheritDoc}
     */
    public function getSourceEntityIdentifier()
    {
        return $this->get(self::FIELD_SOURCE_ENTITY_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function getTotal()
    {
        return $this->get(self::FIELD_TOTAL);
    }
}
