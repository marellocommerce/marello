<?php

namespace Marello\Bundle\WorkflowBundle\Api\Model;

class WorkflowTransit
{
    private ?int $entityId;

    private ?string $workflowName;

    private ?string $transitionName;

    private array $data = [];

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getWorkflowName(): ?string
    {
        return $this->workflowName;
    }

    public function setWorkflowName(string $workflowName): self
    {
        $this->workflowName = $workflowName;

        return $this;
    }

    public function getTransitionName(): string
    {
        return $this->transitionName;
    }

    public function setTransitionName(string $transitionName): self
    {
        $this->transitionName = $transitionName;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
