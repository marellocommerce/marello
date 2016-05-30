<?php

namespace Marello\Component\Sales;

use Marello\Component\Pricing\CurrencyAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;

interface SalesChannelInterface extends CurrencyAwareInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt();

    /**
     * @param \DateTimeInterface $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt);

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt();

    /**
     * @param \DateTimeInterface $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt);

    /**
     * @return string
     */
    public function getChannelType();

    /**
     * @param string $channelType
     *
     * @return $this
     */
    public function setChannelType($channelType);

    /**
     * @return boolean
     */
    public function isActive();

    /**
     * @param boolean $active
     *
     * @return $this
     */
    public function setActive($active);

    /**
     * @return boolean
     */
    public function getDefault();

    /**
     * @param boolean $default
     *
     * @return $this
     */
    public function setDefault($default);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param $code
     * @return $this
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * @return OrganizationInterface
     */
    public function getOwner();

    /**
     * @param OrganizationInterface $owner
     *
     * @return $this
     */
    public function setOwner($owner);
}