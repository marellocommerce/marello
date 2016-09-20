<?php

namespace Marello\Component\Pricing\Model;

use Brick\Math\BigDecimal;

interface PriceInterface extends CurrencyAwareInterface
{
    /**
     * @return BigDecimal
     */
    public function getValue();

    /**
     * @param BigDecimal $value
     * @return $this
     */
    public function setValue(BigDecimal $value);

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
