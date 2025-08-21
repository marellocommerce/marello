<?php

namespace Marello\Bundle\DigitalAssetBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

use Marello\Bundle\DigitalAssetBundle\Async\Topic\DigitalAssetFilesUpdateTopic;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\DigitalAssetBundle\Entity\DigitalAsset;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\EntityExtendBundle\PropertyAccess;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

class DigitalAssetListener
{
    public function __construct(
        protected MessageProducerInterface $messageProducer,
        protected DoctrineHelper $doctrineHelper,
        protected $filesToUpdate = []
    ) {
    }

    public function postPersist(DigitalAsset $digitalAsset, LifecycleEventArgs $args): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $metadata = $args->getObjectManager()->getClassMetadata(DigitalAsset::class);
        foreach ($metadata->associationMappings as $fieldName => $mapping) {
            if (!array_key_exists('targetEntity', $mapping)
                || $mapping['targetEntity'] !== File::class
            ) {
                continue;
            }

            $value = $propertyAccessor->getValue($digitalAsset, $fieldName);
            if (!$value instanceof File) {
                continue;
            }

            $this->updateFileExternalUrl($value, false);
        }
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $unitOfWork = $args->getObjectManager()->getUnitOfWork();
        if (!empty($unitOfWork->getScheduledEntityInsertions())) {
            $records = $this->filterRecords($unitOfWork->getScheduledEntityInsertions());
            $this->applyCallBackForChangeSet([$this, 'updateFileExternalUrl'], $records);
        }
        if (!empty($unitOfWork->getScheduledEntityUpdates())) {
            $records = $this->filterRecords($unitOfWork->getScheduledEntityUpdates());
            $this->applyCallBackForChangeSet([$this, 'updateFileExternalUrl'], $records);
        }
    }

    public function postFlush(): void
    {
        $productsToUpdate = [];
        /** @var File $file */
        foreach ($this->filesToUpdate as $file) {
            $productsToUpdate[] = $file->getParentEntityId();
        }
        $this->filesToUpdate = [];

        foreach (array_unique($productsToUpdate) as $productId) {
            $this->sendToMessageProducer($productId);
        }
    }

    protected function filterRecords(array $records): array
    {
        return array_filter($records, function (object $entity) {
            return $entity instanceof File;
        });
    }

    protected function applyCallBackForChangeSet(callable $callback, array $changeSet): void
    {
        try {
            array_walk($changeSet, $callback);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function updateFileExternalUrl(File $file): void
    {
        if (DigitalAsset::class !== $file->getParentEntityClass()) {
            return;
        }

        $this->filesToUpdate[] = $file;
    }

    protected function sendToMessageProducer(int $digitalAssetId): void
    {
        $this->messageProducer->send(
            DigitalAssetFilesUpdateTopic::getName(),
            ['digitalAssetId' => $digitalAssetId]
        );
    }
}
