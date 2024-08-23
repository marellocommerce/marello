<?php

namespace Marello\Bundle\ProductBundle\Async;

use Doctrine\ORM\EntityManagerInterface;

use Marello\Bundle\ProductBundle\Async\Topic\ProductFilesUpdateTopic;
use Marello\Bundle\ProductBundle\DependencyInjection\Configuration;
use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\AttachmentBundle\Manager\AttachmentManager;
use Oro\Bundle\AttachmentBundle\Manager\ImageResizeManagerInterface;
use Oro\Bundle\AttachmentBundle\Tools\MimeTypeChecker;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\EntityExtendBundle\PropertyAccess;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Psr\Log\LoggerInterface;

class ProductFilesUpdateProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private ConfigManager $configManager,
        private AttachmentManager $attachmentManager,
        private ImageResizeManagerInterface $imageResizeManager,
        private MimeTypeChecker $mimeTypeChecker,
        private array $imagesToApply = []
    ) {
    }

    public static function getSubscribedTopics(): array
    {
        return [ProductFilesUpdateTopic::getName()];
    }

    public function process(MessageInterface $message, SessionInterface $session): string
    {
        if (!$this->configManager->get(Configuration::getConfigKeyByName(Configuration::IMAGE_USE_EXTERNAL_URL_CONFIG))) {
            return self::REJECT;
        }

        $data = JSON::decode($message->getBody());
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($data['productId']);
        if (!$product) {
            return self::REJECT;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $metadata = $this->entityManager->getClassMetadata(Product::class);
        try {
            foreach ($metadata->associationMappings as $fieldName => $mapping) {
                if (!array_key_exists('targetEntity', $mapping)
                    || $mapping['targetEntity'] !== File::class
                ) {
                    continue;
                }

                $file = $propertyAccessor->getValue($product, $fieldName);
                if (!$file instanceof File) {
                    continue;
                }

                $this->processFile($file);
            }

            $this->entityManager->flush();
            foreach ($this->imagesToApply as $image) {
                $this->imageResizeManager->applyFilter($image, 'product_view');
            }
            $this->imagesToApply = [];
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
        $isImage = $this->mimeTypeChecker->isImageMimeType($file->getMimeType());
        if ($isImage) {
            $url = $this->attachmentManager->getFilteredImageUrl($file, 'product_view');
            $this->imagesToApply[] = $file;
        } else {
            $url = $this->attachmentManager->getFileUrl($file);
        }

        // media url is an extended field, so it will not 'show up' in the auto complete
        $file->setMediaUrl($url);
        $this->entityManager->persist($file);
    }
}
