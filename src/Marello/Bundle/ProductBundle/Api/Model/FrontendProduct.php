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
    private $frontendData = [];

    /**
     * @param array $attributes
     * @return $this
     */
    public function setFrontendData(array $data): self
    {
        $this->frontendData = $data;

        return $this;
    }

    /**
     * @return array
     */
    public function getFrontendData(): array
    {
        return $this->frontendData;
    }
}
