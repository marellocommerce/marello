<?php

namespace Marello\Bundle\ShippingBundle\Context\Builder\Basic;

use Marello\Bundle\OrderBundle\Entity\Customer;
use Marello\Bundle\ShippingBundle\Context\Builder\ShippingContextBuilderInterface;
use Marello\Bundle\ShippingBundle\Context\LineItem\Collection\ShippingLineItemCollectionInterface;
use Marello\Bundle\ShippingBundle\Context\ShippingContext;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\LocaleBundle\Model\AddressInterface;

class BasicShippingContextBuilder implements ShippingContextBuilderInterface
{
    /**
     * @var AddressInterface
     */
    private $shippingAddress;

    /**
     * @var AddressInterface
     */
    private $shippingOrigin;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var Price
     */
    private $subTotal;

    /**
     * @var object
     */
    private $sourceEntity;

    /**
     * @var string
     */
    private $sourceEntityIdentifier;

    /**
     * @var ShippingLineItemCollectionInterface
     */
    private $lineItems;

    /**
     * @var AddressInterface
     */
    private $billingAddress;

    /**
     * @var string
     */
    private $paymentMethod;

    /**
     * @var Customer
     */
    private $customer;

    /**
     * @param object $sourceEntity
     * @param string $sourceEntityIdentifier
     */
    public function __construct(
        $sourceEntity,
        $sourceEntityIdentifier
    ) {
        $this->sourceEntity = $sourceEntity;
        $this->sourceEntityIdentifier = $sourceEntityIdentifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()
    {
        $params = $this->getMandatoryParams();
        $params += $this->getOptionalParams();

        return new ShippingContext($params);
    }

    /**
     * {@inheritDoc}
     */
    public function setShippingOrigin(AddressInterface $shippingOrigin)
    {
        $this->shippingOrigin = $shippingOrigin;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setLineItems(ShippingLineItemCollectionInterface $lineItemCollection)
    {
        $this->lineItems = $lineItemCollection;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingAddress(AddressInterface $billingAddress)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setShippingAddress(AddressInterface $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setSubTotal(Price $subTotal)
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return array
     */
    protected function getMandatoryParams()
    {
        $params = [
            ShippingContext::FIELD_SHIPPING_ORIGIN => $this->shippingOrigin,
            ShippingContext::FIELD_SOURCE_ENTITY => $this->sourceEntity,
            ShippingContext::FIELD_SOURCE_ENTITY_ID => $this->sourceEntityIdentifier,
            ShippingContext::FIELD_LINE_ITEMS => $this->lineItems,
        ];

        return $params;
    }

    /**
     * @return array
     */
    protected function getOptionalParams()
    {
        $optionalParams = [
            ShippingContext::FIELD_CURRENCY => $this->currency,
            ShippingContext::FIELD_SUBTOTAL => $this->subTotal,
            ShippingContext::FIELD_BILLING_ADDRESS => $this->billingAddress,
            ShippingContext::FIELD_SHIPPING_ADDRESS => $this->shippingAddress,
            ShippingContext::FIELD_PAYMENT_METHOD => $this->paymentMethod,
            ShippingContext::FIELD_CUSTOMER => $this->customer,
        ];

        // Exclude NULL elements.
        $optionalParams = array_diff_key($optionalParams, array_filter($optionalParams, 'is_null'));

        return $optionalParams;
    }
}
