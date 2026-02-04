<?php

namespace Marello\Bundle\InventoryBundle\Form\EventListener;

use Doctrine\Persistence\ManagerRegistry;
use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Entity\InventoryLevel;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class InventoryItemSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ManagerRegistry $doctrine,
        private AclHelper $aclHelper
    ) {
    }

    /**
     * Get subscribed events
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'submit',
            FormEvents::PRE_SET_DATA => 'setDefaultInventoryLevel'
        ];
    }
    
    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        /** @var InventoryItem $inventoryItem */
        $inventoryItem = $event->getData();
        $externalInventroyLevels = $this->doctrine
            ->getManagerForClass(InventoryLevel::class)
            ->getRepository(InventoryLevel::class)
            ->findExternalLevelsForInventoryItem($inventoryItem, $this->aclHelper);
        if (!empty($externalInventroyLevels)) {
            foreach ($externalInventroyLevels as $inventoryLevel) {
                if (!$inventoryItem->getInventoryLevel($inventoryLevel->getWarehouse())) {
                    $inventoryItem->addInventoryLevel($inventoryLevel);
                }
            }
        }
        $event->setData($inventoryItem);
    }

    public function setDefaultInventoryLevel(FormEvent $event)
    {
        /** @var InventoryItem $inventoryItem */
        $inventoryItem = $event->getData();
        $defaultWarehouse = $this->doctrine
            ->getManagerForClass(Warehouse::class)
            ->getRepository(Warehouse::class)
            ->getDefault($this->aclHelper);
        if (!$inventoryItem->getInventoryLevel($defaultWarehouse)) {
            $level = new InventoryLevel();
            $level->setWarehouse($defaultWarehouse);
            $level->setOrganization($inventoryItem->getOrganization());
            $level->setInventoryQty(0);
            $inventoryItem->addInventoryLevel($level);
        }

        $event->setData($inventoryItem);
    }
}
