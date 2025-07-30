<?php

namespace Marello\Bundle\DigitalAssetBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\DigitalAssetBundle\Entity\DigitalAsset;
use Oro\Bundle\AttachmentBundle\Entity\File;

use Marello\Bundle\ProductBundle\Manager\ProductFileManager;

class DigitalAssetListener
{
    public function __construct(
        private ProductFileManager $productFileManager,
    ) {}

    /** @var array */
    protected $filesToUpdate = [];

    public function onFlush(OnFlushEventArgs $args): void
    {
        $entityManager = $args->getObjectManager();
        $unitOfWork = $entityManager->getUnitOfWork();
        if (!empty($unitOfWork->getScheduledEntityInsertions())) {
            $records = $this->filterRecords($unitOfWork->getScheduledEntityInsertions());
            $this->applyCallBackForChangeSet([$this, 'updateFileExternalUrl'], $records);
        }
        if (!empty($unitOfWork->getScheduledEntityUpdates())) {
            $records = $this->filterRecords($unitOfWork->getScheduledEntityUpdates());
            $this->applyCallBackForChangeSet([$this, 'updateFileExternalUrl'], $records);
        }
    }

    /**
     * @param PostFlushEventArgs $args
     * @return void
     */
    public function postFlush(PostFlushEventArgs $args): void
    {
        if (!empty($this->filesToUpdate)) {
            $entityManager = $args->getObjectManager();
            foreach ($this->filesToUpdate as $file) {
                $url = $this->productFileManager->getFileUrl($file);
                $file->setMediaUrl($url);
                $entityManager->persist($file);
                $this->productFileManager->copyToPublicCache($file);
            }

            $this->filesToUpdate = [];
            $entityManager->flush();
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
}