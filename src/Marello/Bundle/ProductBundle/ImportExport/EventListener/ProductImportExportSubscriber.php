<?php

namespace Marello\Bundle\ProductBundle\ImportExport\EventListener;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\ImportExport\Strategy\ProductStrategy;
use Oro\Bundle\ImportExportBundle\Event\Events;
use Oro\Bundle\ImportExportBundle\Event\NormalizeEntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductImportExportSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            Events::AFTER_NORMALIZE_ENTITY => 'normalizeEntity',
        ];
    }

    public function normalizeEntity(NormalizeEntityEvent $event)
    {
        $object = $event->getObject();
        if (!$object instanceof Product) {
            return;
        }

        $result = $event->getResult();
        if (!empty($result['categories'])) {
            foreach ($result['categories'] as $key => $value) {
                if ($key === 0) {
                    continue;
                }

                $result['categories'][0]['code'] .= ProductStrategy::DELIMITER . $result['categories'][$key]['code'];
                unset($result['categories'][$key]);
            }
        }
        $event->setResultFieldValue('categories', $result['categories']);

        if (!empty($result['channels'])) {
            foreach ($result['channels'] as $key => $value) {
                if ($key === 0) {
                    continue;
                }

                $result['channels'][0]['code'] .= ProductStrategy::DELIMITER . $result['channels'][$key]['code'];
                unset($result['channels'][$key]);
            }
        }
        $event->setResultFieldValue('channels', $result['channels']);
    }
}
