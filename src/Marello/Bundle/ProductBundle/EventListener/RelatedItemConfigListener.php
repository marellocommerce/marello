<?php

namespace Marello\Bundle\ProductBundle\EventListener;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

use Marello\Bundle\ProductBundle\Entity\RelatedItem\UpsellProduct;
use Marello\Bundle\ProductBundle\DependencyInjection\Configuration;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\RelatedProduct;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\CrosssellProduct;
use Marello\Bundle\ProductBundle\Async\Topic\NumberOfRelatedItemsUpdateTopic;

class RelatedItemConfigListener
{
    public function __construct(
        protected ConfigManager $configManager,
        protected DoctrineHelper $doctrineHelper,
        protected MessageProducerInterface $messageProducer
    ) {
    }

    /**
     * @param ConfigUpdateEvent $event
     * @return void
     * @throws \Oro\Component\MessageQueue\Transport\Exception\Exception
     */
    public function onRelatedProductMaxAssignmentChange(ConfigUpdateEvent $event): void
    {
        $key = Configuration::getConfigKeyByName(Configuration::MAX_NUMBER_OF_RELATED_PRODUCTS);
        if (!$event->isChanged($key) || !$event->getNewValue($key)) {
            return;
        }

        if ($event->getNewValue($key) < $event->getOldValue($key)) {
            $this->sendToMessageProducer($event->getNewValue($key), RelatedProduct::class);
        }
    }

    /**
     * @param ConfigUpdateEvent $event
     * @return void
     * @throws \Oro\Component\MessageQueue\Transport\Exception\Exception
     */
    public function onUpsellProductMaxAssignmentChange(ConfigUpdateEvent $event): void
    {
        $key = Configuration::getConfigKeyByName(Configuration::MAX_NUMBER_OF_UPSELL_PRODUCTS);
        if (!$event->isChanged($key) || !$event->getNewValue($key)) {
            return;
        }

        if ($event->getNewValue($key) < $event->getOldValue($key)) {
            $this->sendToMessageProducer($event->getNewValue($key), UpsellProduct::class);
        }
    }

    /**
     * @param ConfigUpdateEvent $event
     * @return void
     * @throws \Oro\Component\MessageQueue\Transport\Exception\Exception
     */
    public function onCrosssellProductMaxAssignmentChange(ConfigUpdateEvent $event): void
    {
        $key = Configuration::getConfigKeyByName(Configuration::MAX_NUMBER_OF_CROSSSELL_PRODUCTS);
        if (!$event->isChanged($key) || !$event->getNewValue($key)) {
            return;
        }

        if ($event->getNewValue($key) < $event->getOldValue($key)) {
            $this->sendToMessageProducer( $event->getNewValue($key), CrosssellProduct::class);
        }
    }

    /**
     * @param int|null $nrOfRelated
     * @param int|null $nrOfUpsell
     * @param int|null $nrOfCrosssell
     * @return void
     * @throws \Oro\Component\MessageQueue\Transport\Exception\Exception
     */
    protected function sendToMessageProducer(?int $nrOfRelated, string $relatedEntityClass): void
    {
        $this->messageProducer->send(
            NumberOfRelatedItemsUpdateTopic::getName(),
            [
                'max_number_of_related_items' => $nrOfRelated,
                'related_entity_class' => $relatedEntityClass
            ]
        );
    }
}
