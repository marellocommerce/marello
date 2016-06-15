<?php

namespace Marello\Component\Product;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

interface VariantInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getVariantCode();

    /**
     * @param string $variantCode
     */
    public function setVariantCode($variantCode);

    /**
     * @return Collection|ProductInterface[]
     */
    public function getProducts();

    /**
     * Add item
     *
     * @param ProductInterface $item
     *
     * @return VariantInterface
     */
    public function addProduct(ProductInterface $item);

    /**
     * Remove item
     *
     * @param ProductInterface $item
     *
     * @return VariantInterface
     */
    public function removeProduct(ProductInterface $item);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt);
}
