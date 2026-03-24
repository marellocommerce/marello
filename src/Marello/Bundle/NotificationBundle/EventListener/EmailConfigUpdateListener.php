<?php

namespace Marello\Bundle\NotificationBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;

use Oro\Bundle\AttachmentBundle\Entity\File;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;
use Oro\Bundle\AttachmentBundle\Manager\AttachmentManager;
use Oro\Bundle\AttachmentBundle\Manager\ImageResizeManagerInterface;

class EmailConfigUpdateListener
{
    private const WATCHED_KEYS = [
        'marello_notification.email_logo'
    ];

    public function __construct(
        private DoctrineHelper $doctrineHelper,
        private AttachmentManager $attachmentManager,
        private EntityManagerInterface $entityManager,
        private ImageResizeManagerInterface $imageResizeManager
    ) {
    }

    public function onConfigUpdate(ConfigUpdateEvent $event): void
    {
        $changedKeys = array_intersect(self::WATCHED_KEYS, array_keys($event->getChangeSet()));
        foreach ($changedKeys as $key) {
            $newValue = $event->getNewValue($key);
            // value is removed from config
            if (!$newValue) {
                continue;
            }

            $file = $this->doctrineHelper
                ->getEntityRepositoryForClass(File::class)
                ->find($newValue);

            if (!$file) {
                continue;
            }

            $this->imageResizeManager->applyFilter($file, 'email_image');
            $this->updatePublicURL($file);

            $this->entityManager->flush();
        }
    }

    private function updatePublicURL(File $file): void
    {
        $url = $this->attachmentManager->getFilteredImageUrl($file, 'email_image');

        $file->setMediaUrl($url);
        $this->entityManager->persist($file);
    }
}
