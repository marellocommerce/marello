<?php

namespace Marello\Bundle\CoreBundle\Async;

use Psr\Log\LoggerInterface;

use Doctrine\ORM\EntityManagerInterface;

use Oro\Component\MessageQueue\Util\JSON;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\EntityExtendBundle\PropertyAccess;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;

use Marello\Bundle\CoreBundle\Async\Topic\FileUpdateTopic;
use Marello\Bundle\ProductBundle\Manager\ProductFileManager;

class FileUpdateProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private ProductFileManager $productFileManager,
        private array $filesToApply = []
    ) {
    }

    public static function getSubscribedTopics(): array
    {
        return [FileUpdateTopic::getName()];
    }

    public function process(MessageInterface $message, SessionInterface $session): string
    {
        $data = JSON::decode($message->getBody());
        $entity = $this->entityManager->getRepository($data['entityClass'])->find($data['entityId']);
        if (!$entity) {
            return self::REJECT;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $metadata = $this->entityManager->getClassMetadata($data['entityClass']);
        try {
            foreach ($metadata->associationMappings as $fieldName => $mapping) {
                if (!array_key_exists('targetEntity', $mapping)
                    || $mapping['targetEntity'] !== File::class
                ) {
                    continue;
                }

                $file = $propertyAccessor->getValue($entity, $fieldName);
                if (!$file instanceof File) {
                    continue;
                }

                $this->processFile($file);
            }

            $this->entityManager->flush();

            foreach ($this->filesToApply as $file) {
                $this->productFileManager->copyToPublicCache($file);
            }

            $this->filesToApply = [];
        } catch (\Exception $e) {
            $this->logger->error(
                'Unexpected exception occurred during updating External Url for Product File',
                ['exception' => $e]
            );

            return self::REJECT;
        }

        return self::ACK;
    }

    private function processFile(File $file): void
    {
        // generate url based on custom public cache directory
        $url = $this->productFileManager->getFileUrl($file);
        $this->filesToApply[] = $file;

        // media url is an extended field, so it will not 'show up' in the auto complete
        $file->setMediaUrl($url);
        $this->entityManager->persist($file);
    }
}
