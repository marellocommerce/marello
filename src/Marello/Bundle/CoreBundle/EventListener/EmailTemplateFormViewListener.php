<?php

namespace Marello\Bundle\CoreBundle\EventListener;

use Oro\Bundle\EmailBundle\Entity\EmailTemplate;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;

class EmailTemplateFormViewListener
{
    /**
     * @param BeforeListRenderEvent $event
     */
    public function onEdit(BeforeListRenderEvent $event)
    {
        $entity = $event->getEntity();

        if (!$entity instanceof EmailTemplate) {
            return;
        }

        $scrollData = $event->getScrollData();
        $scrollData->moveFieldToBlock('template_name', 0);
    }
}
