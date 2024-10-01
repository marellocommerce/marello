<?php

namespace Marello\Bundle\ProductBundle\Manager;

use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\AttachmentBundle\Manager\FileManager;
use Oro\Bundle\AttachmentBundle\Manager\AttachmentManager;
use Oro\Bundle\GaufretteBundle\FileManager as GaufretteFileManager;
/**
 * Manage full process of saving product related files into public cache directories.
 */
class ProductFileManager
{
    public function __construct(
        private GaufretteFileManager $publicMediaCacheManager,
        private AttachmentManager $attachmentManager,
        private FileManager $fileManager,
        private string $directoryPath
    ) {
    }

    /**
     * @param File $file
     * @return void
     */
    public function copyToPublicCache(File $file): void
    {
        if ($file->getExternalUrl() !== null) {
            // Externally stored files cannot be managed.
            return;
        }

        $storagePath = $this->attachmentManager->getFileUrl($file);
        $path = $this->normalizePath($storagePath);

        $content = $this->fileManager->getContent($file);

        if ($content) {
            $this->publicMediaCacheManager->writeToStorage($content, $path);
        }
    }

    /**
     * @param File $file
     * @return string
     */
    public function getFileUrl(File $file): string
    {
        return $this->directoryPath . $file->getId() . DIRECTORY_SEPARATOR . $file->getOriginalFilename();
    }

    private function normalizePath(string $path): string
    {
        // assuming it's original storagepath contains either get, or download
        if (str_contains($path, 'get') || str_contains($path, 'download')) {
            $path = str_replace(['get', 'download'],'files', $path);
        }

        return '/' . ltrim($path, '/');
    }
}
