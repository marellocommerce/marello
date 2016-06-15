<?php

namespace Marello\Component\Product;

interface ProductStatusInterface
{
    /**
     * Get type name
     *
     * @return string
     */
    public function getName();

    /**
     * Set address type label
     *
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label);

    /**
     * Get service type label
     *
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function __toString();
}
