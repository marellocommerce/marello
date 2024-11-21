<?php

namespace Marello\Bundle\ProductBundle\ImportExport\Serializer;

use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\AttachmentBundle\Exception\ExternalFileNotAccessibleException;
use Oro\Bundle\AttachmentBundle\Manager\FileManager;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class ProductImageNormalizer implements ContextAwareNormalizerInterface, ContextAwareDenormalizerInterface
{
    public function __construct(
        private FileManager $fileManager
    ) {
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof File && $data->getParentEntityClass() === Product::class;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var File $object */
        return [
            'externalUrl' => $object->getExternalUrl(),
        ];
    }

    public function supportsDenormalization($data, string $type, ?string $format = null, array $context = []): bool
    {
        if ($type === File::class && isset($context['entityName']) && $context['entityName'] === Product::class) {
            return true;
        }

        return false;
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = []): File
    {
        $file = new File();
        try {
            $this->fileManager->setFileFromPath($file, $data['externalUrl']);
        } catch (ExternalFileNotAccessibleException $e) {
            // Do nothing, just return an empty file that would be processed in ProductStrategy
        }

        return $file;
    }
}
