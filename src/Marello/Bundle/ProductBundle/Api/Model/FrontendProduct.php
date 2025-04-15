<?php

namespace Marello\Bundle\ProductBundle\Api\Model;

use Marello\Bundle\ProductBundle\Entity\Product;

/**
 * The model for frontend API resource to retrieve API access key by pos user email/username and password.
 */
class FrontendProduct extends Product
{
    /**
     * @var array
     */
    private $frontendAttributes = [];

    /**
     * @param array $attributes
     * @return $this
     */
    public function setFrontendAttribute(array $attributes): self
    {
        $this->frontendAttributes = $attributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getFrontendAttributes(): array
    {
        return $this->frontendAttributes;
    }
}
