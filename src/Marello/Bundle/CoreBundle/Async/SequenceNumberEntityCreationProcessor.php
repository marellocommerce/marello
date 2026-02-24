<?php

namespace Marello\Bundle\CoreBundle\Async;

use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;

use Oro\Component\MessageQueue\Util\JSON;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;

use Marello\Bundle\CoreBundle\Provider\SequenceNumberProvider;
use Marello\Bundle\CoreBundle\Async\Topic\SequenceNumberEntityCreationTopic;

class SequenceNumberEntityCreationProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private SequenceNumberProvider $sequenceNumberProvider
    ) {
    }

    public static function getSubscribedTopics(): array
    {
        return [SequenceNumberEntityCreationTopic::getName()];
    }

    public function process(MessageInterface $message, SessionInterface $session): string
    {
        $data = JSON::decode($message->getBody());
        /** @var Organization $organization */
        $organization = $this->entityManager->getRepository(Organization::class)->find($data['organizationId']);
        if (!$organization) {
            return self::REJECT;
        }

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
        try {
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
        } catch (\Exception $e) {
            $this->logger->error(
                'Unexpected exception occurred during creating the ',
                ['exception' => $e]
            );

            return self::REJECT;
        }

        return self::ACK;
    }
}
