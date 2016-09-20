<?php

namespace Marello\Component\RMA\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Marello\Component\Order\Model\OrderInterface;
use Marello\Component\RMA\Model\ExtendReturnEntity;
use Marello\Component\RMA\Model\ReturnEntityInterface;
use Marello\Component\RMA\Model\ReturnItemInterface;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;

/**
 * @Oro\Config()
 */
class ReturnEntity extends ExtendReturnEntity implements ReturnEntityInterface
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var string
     */
    protected $returnNumber;

    /**
     * @var Collection|ReturnItemInterface[]
     */
    protected $returnItems;

    /**
     * @var WorkflowItem
     */
    protected $workflowItem;

    /**
     * @var WorkflowStep
     */
    protected $workflowStep;

    /**
     * @var \DateTime
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.created_at"
     *          }
     *      }
     * )
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.updated_at"
     *          }
     *      }
     * )
     */
    protected $updatedAt;

    /**
     * ReturnEntity constructor.
     */
    public function __construct()
    {
        $this->returnItems = new ArrayCollection();
    }

    /**
     * Copies product sku and name to attributes within this return item.
     */
    public function prePersist()
    {
        $this->createdAt = $this->updatedAt = new \DateTime();
    }
    
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param OrderInterface $order
     *
     * @return $this
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * @return string
     */
    public function getReturnNumber()
    {
        return $this->returnNumber;
    }

    /**
     * @param string $returnNumber
     *
     * @return $this
     */
    public function setReturnNumber($returnNumber)
    {
        $this->returnNumber = $returnNumber;

        return $this;
    }

    /**
     * @return Collection|ReturnItemInterface[]
     */
    public function getReturnItems()
    {
        return $this->returnItems;
    }

    /**
     * @param ReturnItemInterface $item
     *
     * @return $this
     */
    public function addReturnItem(ReturnItemInterface $item)
    {
        $this->returnItems->add($item->setReturn($this));

        return $this;
    }

    /**
     * @param ReturnItemInterface $item
     *
     * @return $this
     */
    public function removeReturnItem(ReturnItemInterface $item)
    {
        $this->returnItems->removeElement($item);

        return $this;
    }

    /**
     * @return WorkflowItem
     */
    public function getWorkflowItem()
    {
        return $this->workflowItem;
    }

    /**
     * @param WorkflowItem $workflowItem
     *
     * @return $this
     */
    public function setWorkflowItem($workflowItem)
    {
        $this->workflowItem = $workflowItem;

        return $this;
    }

    /**
     * @return WorkflowStep
     */
    public function getWorkflowStep()
    {
        return $this->workflowStep;
    }

    /**
     * @param WorkflowStep $workflowStep
     *
     * @return $this
     */
    public function setWorkflowStep($workflowStep)
    {
        $this->workflowStep = $workflowStep;

        return $this;
    }
}
