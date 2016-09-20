<?php

namespace Marello\Component\Pricing\Entity;

use Brick\Math\BigDecimal;
use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Pricing\Model\PriceInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

class BasePrice implements PriceInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var BigDecimal
     */
    protected $value;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var \DateTime $createdAt
     */
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     */
    protected $updatedAt;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return BigDecimal
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param BigDecimal $value
     * @return $this
     */
    public function setValue(BigDecimal $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
