<?php

namespace Marello\Bundle\CoreBundle\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\DistributionBundle\Handler\ApplicationState;

use Marello\Bundle\CoreBundle\Provider\SequenceNumberProvider;

class OrganizationCreateListener
{
    public function __construct(
        protected ApplicationState $applicationState,
        protected SequenceNumberProvider $sequenceNumberProvider
    ) {
    }

    /**
     * @param Organization $organization
     * @param LifecycleEventArgs $args
     */
    public function postPersist(Organization $organization, LifecycleEventArgs $args)
    {
        if ($this->applicationState->isInstalled()) {
            $typesToGenerate = [
                'invoice',
                'order',
                'allocation',
                'shipment',
                'packingslip',
                'refund',
                'return',
                'purchaseorder',
                'replenishmentorder'
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
}
