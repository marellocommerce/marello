<?php

namespace Marello\Component\Sales\ORM\Provider;

use Doctrine\Common\Persistence\ObjectManager;

use Marello\Component\Sales\Entity\SalesChannel;
use Marello\Component\Product\Model\ProductInterface;
use Marello\Component\Sales\Provider\ChannelProviderInterface;
use Marello\Component\Sales\Model\SalesChannelInterface;

class ChannelProvider implements ChannelProviderInterface
{
    /** @var ObjectManager $manager */
    protected $manager;

    /**
     * ChannelProvider constructor.
     * @param ObjectManager $manager
     */
    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Returns ids of all related sales channels for a product.
     *
     * @param ProductInterface $product
     *
     * @return array $ids
     */
    public function getSalesChannelsIds(ProductInterface $product)
    {
        $ids = [];
        $product
            ->getChannels()
            ->map(function (SalesChannelInterface $channel) use (&$ids) {
                $ids[] = $channel->getId();
            });

        return $ids;
    }

    /**
     * Returns ids of all sales channels which are not in related to a product.
     *
     * @param ProductInterface $product
     *
     * @return array $ids
     */
    public function getExcludedSalesChannelsIds(ProductInterface $product)
    {
        $relatedIds = $this->getSalesChannelsIds($product);
        $excludedIds = [];

        $ids = $this->manager
            ->getRepository(SalesChannel::class)
            ->findExcludedSalesChannelIds($relatedIds);

        foreach ($ids as $k => $v) {
            $excludedIds[] = $v['id'];
        }

        return $excludedIds;
    }
}
