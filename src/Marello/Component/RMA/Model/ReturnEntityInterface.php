<?php

namespace Marello\Component\RMA\Model;

use Doctrine\Common\Collections\Collection;
use Marello\Component\Order\Model\OrderInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;

interface ReturnEntityInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @return OrderInterface
     */
    public function getOrder();

    /**
     * @param OrderInterface $order
     *
     * @return $this
     */
    public function setOrder(OrderInterface $order);

    /**
     * @return string
     */
    public function getReturnNumber();

    /**
     * @param string $returnNumber
     *
     * @return $this
     */
    public function setReturnNumber($returnNumber);

    /**
     * @return Collection|ReturnItemInterface[]
     */
    public function getReturnItems();

    /**
     * @param ReturnItemInterface $item
     *
     * @return $this
     */
    public function addReturnItem(ReturnItemInterface $item);

    /**
     * @param ReturnItemInterface $item
     *
     * @return $this
     */
    public function removeReturnItem(ReturnItemInterface $item);

    /**
     * @return WorkflowItem
     */
    public function getWorkflowItem();

    /**
     * @param WorkflowItem $workflowItem
     *
     * @return $this
     */
    public function setWorkflowItem($workflowItem);

    /**
     * @return WorkflowStep
     */
    public function getWorkflowStep();

    /**
     * @param WorkflowStep $workflowStep
     *
     * @return $this
     */
    public function setWorkflowStep($workflowStep);
}
