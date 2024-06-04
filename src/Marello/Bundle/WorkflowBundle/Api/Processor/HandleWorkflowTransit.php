<?php

namespace Marello\Bundle\WorkflowBundle\Api\Processor;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Marello\Bundle\WorkflowBundle\Api\Model\WorkflowTransit;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowDefinition;
use Oro\Bundle\WorkflowBundle\Exception\ForbiddenTransitionException;
use Oro\Bundle\WorkflowBundle\Exception\WorkflowException;
use Oro\Bundle\WorkflowBundle\Exception\WorkflowNotFoundException;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;
use Oro\Bundle\WorkflowBundle\Model\WorkflowRegistry;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;
use Oro\Bundle\ApiBundle\Processor\Create\CreateContext;

class HandleWorkflowTransit implements ProcessorInterface
{
    public function __construct(
        private ManagerRegistry $registry,
        private WorkflowManager $workflowManager,
        private WorkflowRegistry $workflowRegistry
    ) {}

    public function process(ContextInterface $context)
    {
        /** @var CreateContext $context */
        $model = $context->getResult();
        if (!$model instanceof WorkflowTransit) {
            return;
        }

        $entityId = $model->getEntityId();
        $workflowName = $model->getWorkflowName();
        $transitionName = $model->getTransitionName();

        $workflowDefinition = $this->registry->getRepository(WorkflowDefinition::class)->findOneBy(['name' => $workflowName]);
        if (!$workflowDefinition) {
            throw new WorkflowNotFoundException($workflowName);
        }

        $entityManager = $this->registry->getManagerForClass($workflowDefinition->getRelatedEntity());
        $entity = $entityManager->getRepository($workflowDefinition->getRelatedEntity())->find($entityId);
        if (!$entity) {
            throw new EntityNotFoundException(sprintf(
                'Entity "%s" with id %d not found.',
                $workflowDefinition->getRelatedEntity(),
                $entityId
            ));
        }

        $workflowItem = $this->workflowManager->getWorkflowItem($entity, $workflowName);
        if (!$workflowItem) {
            throw new WorkflowException(sprintf(
                'Workflow item for workflow "%s" and for entity "%s" with id %d not found.',
                $workflowName,
                $workflowDefinition->getRelatedEntity(),
                $entityId
            ));
        }

        $workflow = $this->workflowRegistry->getWorkflow($workflowItem->getWorkflowName());
        $isAllowed = $workflow->isTransitionAllowed($workflowItem, $transitionName);
        if (!$isAllowed) {
            throw new ForbiddenTransitionException('Transition is not allowed');
        }

        $workflow->transit($workflowItem, $transitionName);

        $entityManager->flush();
    }
}
