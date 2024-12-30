<?php

namespace Marello\Bundle\ProductBundle\Async;

use Oro\Component\MessageQueue\Util\JSON;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;

use Marello\Bundle\ProductBundle\Async\Topic\NumberOfRelatedItemsUpdateTopic;

class UpdateRelatedItemsProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    public function __construct(private DoctrineHelper $doctrineHelper)
    {
    }

    /**
     * @return array
     */
    public static function getSubscribedTopics(): array
    {
        return [NumberOfRelatedItemsUpdateTopic::getName()];
    }

    /**
     * @param MessageInterface $message
     * @param SessionInterface $session
     * @return string
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \JsonException
     */
    public function process(MessageInterface $message, SessionInterface $session): string
    {
        $data = JSON::decode($message->getBody());
        if (!isset($data['max_number_of_related_items']) || !isset($data['related_entity_class'])) {
            return self::REJECT;
        }

        $nrOfRelatedItems = $data['max_number_of_related_items'];
        $relatedItemClass = $data['related_entity_class'];

        $qb = $this
            ->doctrineHelper
            ->getEntityManagerForClass($relatedItemClass)
            ->createQueryBuilder();

        $qb->select('DISTINCT IDENTITY(rp.product) as id')
            ->from($relatedItemClass, 'rp')
            ->orderBy('rp.product');
        $productIds = $qb->getQuery()->getArrayResult();
        $productIds = array_column($productIds, 'id');

        foreach ($productIds as $productId) {
            $allRelatedItems = $this
                ->doctrineHelper
                ->getEntityRepositoryForClass($relatedItemClass)
                ->findBy(['product' => $productId]);

            if (count($allRelatedItems) > $nrOfRelatedItems) {
                $i = 1;
                foreach ($allRelatedItems as $relatedItem) {
                    if ($i <= $nrOfRelatedItems) {
                        $i++;
                        continue;
                    }

                    $this->doctrineHelper
                        ->getEntityManagerForClass($relatedItemClass)
                        ->remove($relatedItem);
                }
            }
        }

        $this->doctrineHelper
            ->getEntityManagerForClass($relatedItemClass)
            ->flush();

        return self::ACK;
    }
}
