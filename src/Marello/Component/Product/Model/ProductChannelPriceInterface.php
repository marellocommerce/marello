<?php

namespace Marello\Component\Product\Model;

use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Sales\SalesChannelInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;

interface ProductChannelPriceInterface
{
    /**
     * @return SalesChannelInterface
     */
    public function getChannel();

    /**
     * @param SalesChannelInterface $channel
     *
     * @return $this
     */
    public function setChannel(SalesChannelInterface $channel);
}
