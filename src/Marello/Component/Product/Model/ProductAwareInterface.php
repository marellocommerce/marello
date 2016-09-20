<?php

namespace Marello\Component\Product\Model;

use Marello\Component\Product\Model\ProductInterface;

interface ProductAwareInterface
{
    /**
     * @return ProductInterface
     */
    public function getProduct();

    /**
     * @param ProductInterface $product
     * @return $this
     */
    public function setProduct(ProductInterface $product);
}
