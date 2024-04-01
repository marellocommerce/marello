<?php

namespace Marello\Bundle\InventoryBundle\Api\Processor;

use Marello\Bundle\InventoryBundle\Entity\InventoryLevel;
use Marello\Bundle\InventoryBundle\Event\InventoryUpdateEvent;
use Marello\Bundle\InventoryBundle\Model\InventoryUpdateContextFactory;
use Oro\Bundle\ApiBundle\Processor\CustomizeFormData\CustomizeFormDataContext;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CustomizeInventoryLevelFormData implements ProcessorInterface
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected array $inventoryQtyAdjustmentMap = []
    ) {}

    public function process(ContextInterface $context): void
    {
        /** @var CustomizeFormDataContext $context */
        $config = $context->getConfig();
        if (null === $config) {
            // not supported API resource
            return;
        }

        $inventoryLevel = $context->getForm()->getData();
        if (!$inventoryLevel instanceof InventoryLevel || !$inventoryLevel->getId()) {
            return;
        }

        $data = $context->getData();
        if ($context->getFirstGroup() === CustomizeFormDataContext::EVENT_PRE_SUBMIT
            && array_key_exists('inventoryQty', $data)
        ) {
            $this->inventoryQtyAdjustmentMap[$inventoryLevel->getId()] = $data['inventoryQty'];
            unset($data['inventoryQty']);
            $context->setData($data);

            return;
        }

        if ($context->getFirstGroup() === CustomizeFormDataContext::EVENT_SUBMIT
            && array_key_exists($inventoryLevel->getId(), $this->inventoryQtyAdjustmentMap)
        ) {
            $adjustment = $this->inventoryQtyAdjustmentMap[$inventoryLevel->getId()];
            unset($this->inventoryQtyAdjustmentMap[$inventoryLevel->getId()]);

            if ($adjustment === 0) {
                return;
            }

            $context = InventoryUpdateContextFactory::createInventoryLevelUpdateContext(
                $inventoryLevel,
                $inventoryLevel->getInventoryItem(),
                [],
                $adjustment,
                0,
                'api'
            );

            $this->eventDispatcher->dispatch(
                new InventoryUpdateEvent($context),
                InventoryUpdateEvent::NAME
            );
        }
    }
}
