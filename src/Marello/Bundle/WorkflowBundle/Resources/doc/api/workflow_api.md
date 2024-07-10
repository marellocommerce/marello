# Marello\Bundle\WorkflowBundle\Api\Model\WorkflowTransit

## ACTIONS

### create

Transit workflow step

{@request:json_api}
Example of the request:

```JSON
{
  "meta": {
    "entityId": 42,
    "workflowName": "some_workflow",
    "transitionName": "some_transition"
  }
}
```
{@/request}

## FIELDS

### entityId

Entity ID which would be used to transit through a workflow

**The required field.**

### workflowName

Workflow name. See WorkflowDefinition::relatedEntity field.

**The required field.**

### transitionName

Workflow transition name.

