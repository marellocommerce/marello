<?php

namespace Marello\Bundle\WorkflowBundle\Api\Model;

class WorkflowTransit
{
    private ?int $entityId;

    private ?string $workflowName;

    private ?string $transitionName;

    private bool $success = false;

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

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): self
    {
        $this->success = $success;

        return $this;
    }
}
