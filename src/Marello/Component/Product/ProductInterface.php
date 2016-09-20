<?php

namespace Marello\Component\Product;

use Doctrine\Common\Collections\Collection;
use Marello\Bundle\ProductBundle\Entity\ProductStatus;
use Marello\Component\Inventory\Model\InventoryItemInterface;
use Marello\Component\Sales\SalesChannelAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;

interface ProductInterface extends
    SalesChannelAwareInterface,
    PricingAwareInterface
{
    /**
     * @param int $id
     *
     * @return ProductInterface
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param string $name
     *
     * @return ProductInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $sku
     *
     * @return ProductInterface
     */
    public function setSku($sku);

    /**
     * @return string
     */
    public function getSku();

    /**
     * @param string $type
     *
     * @return ProductInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param float $cost
     *
     * @return ProductInterface
     */
    public function setCost($cost);

    /**
     * @return float
     */
    public function getCost();

    /**
     * @param float $price
     *
     * @return ProductInterface
     */
    public function setPrice($price);

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     *
     * @return ProductInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get contact last update date/time
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime updatedAt
     *
     * @return ProductInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return ProductStatus
     */
    public function getStatus();

    /**
     * @param ProductStatus $status
     *
     * @return ProductInterface
     */
    public function setStatus(ProductStatus $status);

    /**
     * @return VariantInterface
     */
    public function getVariant();

    /**
     * @param VariantInterface $variant
     *
     * @return ProductInterface
     */
    public function setVariant(VariantInterface $variant = null);

    /**
     * @return OrganizationInterface
     */
    public function getOrganization();

    /**
     * @param OrganizationInterface $organization
     * @return ProductInterface
     */
    public function setOrganization(OrganizationInterface $organization);

    /**
     * @param array $data
     * @return ProductInterface
     */
    public function setData(array $data);

    /**
     * @return array
     */
    public function getData();

    /**
     * @return string
     */
    public function __toString();

    /**
     * @return Collection|InventoryItemInterface[]
     */
    public function getInventoryItems();

    /**
     * @param InventoryItemInterface $item
     *
     * @return ProductInterface
     */
    public function addInventoryItem(InventoryItemInterface $item);

    /**
     * @param InventoryItemInterface $item
     *
     * @return $this
     */
    public function removeInventoryItem(InventoryItemInterface $item);
}
