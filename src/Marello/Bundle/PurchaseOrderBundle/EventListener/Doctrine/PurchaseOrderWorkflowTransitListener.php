<?php

namespace Marello\Bundle\PurchaseOrderBundle\EventListener\Doctrine;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;
use Oro\Bundle\WorkflowBundle\Model\WorkflowStartArguments;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Marello\Bundle\PurchaseOrderBundle\Entity\PurchaseOrder;
use Marello\Bundle\PurchaseOrderBundle\Entity\PurchaseOrderItem;

class PurchaseOrderWorkflowTransitListener
{
    use ApplicableWorkflowsTrait;

    public function __construct(
        protected ConfigManager $configManager,
        protected WorkflowManager $workflowManager,
        protected array $entitiesScheduledForWorkflowStart = []
    ) {
    }

    public function postPersist(PurchaseOrder $entity): void
    {
        $addToSchedule = false;
        if ($entity->getSupplier()->getPoSendBy() === Supplier::SEND_PO_MANUALLY) {
            $addToSchedule = true;
        } elseif ($this->configManager->get('marello_purchaseorder.send_directly')) {
            $onDemandItems = [];
            /** @var PurchaseOrderItem[] $poItems */
            $poItems = $entity->getItems()->toArray();
            foreach ($poItems as $poItem) {
                if ($this->isOrderOnDemandAllowed($poItem->getProduct())) {
                    $onDemandItems[] = $poItem;
                }
            }

            $addToSchedule = count($onDemandItems) === count($poItems);
        }

        if (!$addToSchedule) {
            return;
        }

        $workflow = $this->getApplicableWorkflow($entity);
        if (!$workflow) {
            return;
        }

        $this->entitiesScheduledForWorkflowStart[] = new WorkflowStartArguments(
            $workflow->getName(),
            $entity,
            [],
            PurchaseOrder::SEND_TRANSITION
        );
    }

    public function postFlush(): void
    {
        if (empty($this->entitiesScheduledForWorkflowStart)) {
            return;
        }

        $massStartData = $this->entitiesScheduledForWorkflowStart;
        $this->entitiesScheduledForWorkflowStart = [];
        $this->workflowManager->massStartWorkflow($massStartData);
    }

    private function isOrderOnDemandAllowed(Product $product): bool
    {
        $inventoryItem = $product->getInventoryItem();
        if ($inventoryItem && $inventoryItem->isOrderOnDemandAllowed()) {
            return true;
        }

        return false;
    }
}
