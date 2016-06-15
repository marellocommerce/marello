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
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

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
     * @return bool
     */
    public function isActive();

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive($active);

    /**
     * @return bool
     */
    public function getDefault();

    /**
     * @param bool $default
     *
     * @return $this
     */
    public function setDefault($default);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code);

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
    public function setOwner(OrganizationInterface $owner);
}