<?php

namespace Marello\Bundle\CoreBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

use Marello\Bundle\CoreBundle\Provider\SequenceNumberProvider;
use Marello\Bundle\CoreBundle\Async\Topic\SequenceNumberEntityCreationTopic;

class OrganizationCreateListener
{
    public function __construct(
        protected SequenceNumberProvider $sequenceNumberProvider
    ) {
    }

    /**
     * @param Organization $organization
     * @param LifecycleEventArgs $args
     */
    public function postPersist(Organization $organization, LifecycleEventArgs $args)
    {
        $typesToGenerate = [
            'invoice',
            'order',
            'allocation',
            'shipment',
            'packingslip',
            'refund',
            'return',
            'purchaseorder'
        ];
        $update = false;
        foreach ($typesToGenerate as $k => $value) {
            if ($k === array_key_last($typesToGenerate)) {
                $update = true;
            }
            $this->sequenceNumberProvider->generateNewSequenceEntityAndTable(
                $value,
                $organization->getId(),
                $update
            );
        }
    }
}
