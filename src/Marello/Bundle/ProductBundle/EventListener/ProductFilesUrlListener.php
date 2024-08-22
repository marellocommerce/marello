<?php

namespace Marello\Bundle\ProductBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Marello\Bundle\ProductBundle\Async\Topic\ProductFilesUpdateTopic;
use Marello\Bundle\ProductBundle\DependencyInjection\Configuration;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;
use Oro\Bundle\EntityExtendBundle\PropertyAccess;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

class ProductFilesUrlListener
{
    public function __construct(
        protected ConfigManager $configManager,
        protected MessageProducerInterface $messageProducer,
        protected DoctrineHelper $doctrineHelper,
        protected array $filesToUpdate = [],
    ) {
    }

    public function postPersist(Product $product, LifecycleEventArgs $args): void
    {
        if (!$this->configManager->get(Configuration::getConfigKeyByName(Configuration::IMAGE_USE_EXTERNAL_URL_CONFIG))) {
            return;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $metadata = $args->getObjectManager()->getClassMetadata(Product::class);
        foreach ($metadata->associationMappings as $fieldName => $mapping) {
            if (!array_key_exists('targetEntity', $mapping)
                || $mapping['targetEntity'] !== File::class
            ) {
                continue;
            }

            $value = $propertyAccessor->getValue($product, $fieldName);
            if (!$value instanceof File) {
                continue;
            }

            $this->updateImageFileExternalUrl($value, false);
        }
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        if (!$this->configManager->get(Configuration::getConfigKeyByName(Configuration::IMAGE_USE_EXTERNAL_URL_CONFIG))) {
            return;
        }

        $unitOfWork = $args->getObjectManager()->getUnitOfWork();
        if (!empty($unitOfWork->getScheduledEntityInsertions())) {
            $records = $this->filterRecords($unitOfWork->getScheduledEntityInsertions());
            $this->applyCallBackForChangeSet([$this, 'updateImageFileExternalUrl'], $records);
        }
        if (!empty($unitOfWork->getScheduledEntityUpdates())) {
            $records = $this->filterRecords($unitOfWork->getScheduledEntityUpdates());
            $this->applyCallBackForChangeSet([$this, 'updateImageFileExternalUrl'], $records);
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

    public function onConfigUpdate(ConfigUpdateEvent $event): void
    {
        $key = Configuration::getConfigKeyByName(Configuration::IMAGE_USE_EXTERNAL_URL_CONFIG);
        if (!$event->isChanged($key) || !$event->getNewValue($key)) {
            return;
        }

        foreach ($this->getProductsToProcess() as $product) {
            // if the setting is changed, we need to update all the images
            // of products to regenerate the media urls
            $this->sendToMessageProducer($product['id']);
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

    protected function updateImageFileExternalUrl(File $file, bool $checkParent = true): void
    {
        if ($checkParent && Product::class !== $file->getParentEntityClass()) {
            return;
        }

        $this->filesToUpdate[] = $file;
    }

    protected function getProductsToProcess(): iterable
    {
        /** @var ProductRepository $qb */
        $em = $this->doctrineHelper->getEntityRepositoryForClass(Product::class);
        $this->doctrineHelper
            ->getEntityManager(Product::class)
            ->getConnection()
            ->getConfiguration()
            ->setSQLLogger(); //turn off log

        $qb = $em->createQueryBuilder('p');
        $query = $qb->select('p.id', 'p.sku');

        return $query->getQuery()->toIterable();
    }

    protected function sendToMessageProducer(int $productId): void
    {
        $this->messageProducer->send(
            ProductFilesUpdateTopic::getName(),
            ['productId' => $productId]
        );
    }
}
