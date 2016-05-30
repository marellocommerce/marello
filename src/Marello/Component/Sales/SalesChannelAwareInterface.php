<?php

namespace Marello\Component\Sales;

use Doctrine\Common\Collections\Collection;

use Marello\Bundle\SalesBundle\Entity\SalesChannel;

interface SalesChannelAwareInterface
{
    /**
     * @param SalesChannelInterface $channel
     *
     * @return $this
     */
    public function addChannel(SalesChannelInterface $channel);

    /**
     * @param SalesChannelInterface $channel
     *
     * @return $this
     */
    public function removeChannel(SalesChannelInterface $channel);

    /**
     * @return Collection|SalesChannel[]
     */
    public function getChannels();

    /**
     * @return bool
     */
    public function hasChannels();
}
