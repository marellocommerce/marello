<?php

namespace Marello\Component\Product;

use Marello\Component\Product\ProductInterface;

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