<?php

namespace Marello\Component\Inventory\Logging;

use Marello\Component\Product\ProductInterface;

interface ChartBuilderInterface
{
    /**
     * Returns data in format ready for inventory chart.
     *
     * @param ProductInterface   $product
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return array
     */
    public function getChartData(ProductInterface $product, \DateTime $from, \DateTime $to);
}
