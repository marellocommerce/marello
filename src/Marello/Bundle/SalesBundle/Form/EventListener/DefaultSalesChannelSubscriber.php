<?php

namespace Marello\Bundle\SalesBundle\Form\EventListener;

use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\SalesBundle\Model\SalesChannelsAwareInterface;

class DefaultSalesChannelSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected EntityManager $em,
        protected AclHelper $aclHelper
    ) {
    }

    /**
     * Get subscribed events
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA    => 'preSetData'
        ];
    }

    /**
     * Preset data for channels
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $entity = $event->getData();
        if (!$entity || null === $entity->getId()) {
            if ($entity instanceof SalesChannelsAwareInterface) {
                $channels = $this->getDefaultChannels();
                if (!is_null($channels) && count($channels) !== 0) {
                    foreach ($channels as $channel) {
                        $entity->addChannel($channel);
                    }
                }
                $event->setData($entity);
            }
        }
    }

    /**
     * Get default channels for new products.
     * @return array
     */
    public function getDefaultChannels()
    {
        return $this->em
            ->getRepository(SalesChannel::class)
            ->getDefaultActiveChannels($this->aclHelper);
    }
}
