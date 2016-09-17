<?php

namespace Marello\Component\Pricing;

use Brick\Math\BigNumber;

interface PriceInterface extends CurrencyAwareInterface
{
    /**
     * @return BigNumber
     */
    public function getValue();

    /**
     * @param BigNumber $value
     * @return $this
     */
    public function setValue(BigNumber $value);

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
