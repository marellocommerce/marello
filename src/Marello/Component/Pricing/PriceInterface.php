<?php

namespace Marello\Component\Pricing;

interface PriceInterface extends CurrencyAwareInterface
{
    /**
     * @return float
     */
    public function getValue();

    /**
     * @param float $value
     * @return $this
     */
    public function setValue($value);

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}