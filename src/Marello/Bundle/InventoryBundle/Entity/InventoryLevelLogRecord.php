<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * @Oro\Config(
 *      defaultValues={
 *          "entity"={
 *              "icon"="fa-list-alt"
 *          }
 *      }
 * )
 */
#[ORM\Table(name: 'marello_inventory_level_log')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\InventoryBundle\Entity\Repository\InventoryLevelLogRecordRepository::class)]
#[ORM\HasLifecycleCallbacks]
class InventoryLevelLogRecord
{
    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     *
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    protected $id;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="marello.inventory.inventorylevel.entity_label"
     *          },
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     *
     * @var InventoryLevel
     */
    #[ORM\JoinColumn(name: 'inventory_level_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\InventoryBundle\Entity\InventoryLevel::class, inversedBy: 'inventoryLevelLogRecords')]
    protected $inventoryLevel;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="marello.inventory.inventoryitem.entity_label"
     *          },
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     *
     * @var InventoryItem
     */
    #[ORM\JoinColumn(name: 'inventory_item_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Marello\Bundle\InventoryBundle\Entity\InventoryItem::class, cascade: ['persist', 'remove'])]
    protected $inventoryItem;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'warehouse_name', type: 'string', nullable: false)]
    protected $warehouseName;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     * @var int
     */
    #[ORM\Column(name: 'inventory_alteration', type: 'integer')]
    protected $inventoryAlteration;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     * @var int
     */
    #[ORM\Column(name: 'allocated_inventory_alteration', type: 'integer')]
    protected $allocatedInventoryAlteration;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'change_trigger', type: 'string')]
    protected $changeTrigger;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     *
     * @var User
     */
    #[ORM\JoinColumn(name: 'user_id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \Oro\Bundle\UserBundle\Entity\User::class)]
    protected $user = null;

    /**
     * Subject field could be filled by a listener.
     *
     * @see \Marello\Bundle\InventoryBundle\EventListener\Doctrine\StockLevelSubjectHydrationSubscriber
     *
     * @var mixed
     */
    protected $subject = null;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'subject_type', type: 'string', nullable: true)]
    protected $subjectType = null;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     * @var int
     */
    #[ORM\Column(name: 'subject_id', type: 'integer', nullable: true)]
    protected $subjectId = null;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     * )
     * @var string
     */
    #[ORM\Column(name: 'inventory_batch', type: 'string', nullable: true)]
    protected $inventoryBatch;

    /**
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.created_at"
     *          },
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     * @var \DateTime
     */
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    protected $createdAt;

    /**
     * @var \DateTime $updatedAt
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "entity"={
     *              "label"="oro.ui.updated_at"
     *          }
     *      }
     * )
     * @var \DateTime
     */
    #[ORM\Column(type: 'datetime', name: 'updated_at')]
    protected $updatedAt;

    /**
     * InventoryLevel constructor.
     *
     * @param InventoryLevel $inventoryLevel
     * @param int           $inventoryAlt
     * @param int           $allocatedInventoryAlt
     * @param string        $changeTrigger
     * @param User          $user
     * @param mixed|null    $subject
     */
    public function __construct(
        InventoryLevel $inventoryLevel,
        $inventoryAlt,
        $allocatedInventoryAlt,
        $changeTrigger,
        User $user = null,
        $subject = null,
        $inventoryBatch = null
    ) {
        $this->inventoryLevel               = $inventoryLevel;
        $this->inventoryAlteration          = $inventoryAlt;
        $this->allocatedInventoryAlteration = $allocatedInventoryAlt;
        $this->changeTrigger                = $changeTrigger;
        $this->user                         = $user;
        $this->subject                      = $subject;
        $this->inventoryItem                = $inventoryLevel->getInventoryItem();
        $this->warehouseName                = $inventoryLevel->getWarehouse()->getLabel();
        $this->inventoryBatch               = $inventoryBatch;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return InventoryLevel
     */
    public function getInventoryLevel(): InventoryLevel
    {
        return $this->inventoryLevel;
    }

    /**
     * @return int
     */
    public function getInventoryDiff(): int
    {
        return $this->inventoryAlteration;
    }

    /**
     * @return int
     */
    public function getAllocatedInventoryDiff(): int
    {
        return $this->allocatedInventoryAlteration;
    }

    /**
     * @return string
     */
    public function getChangeTrigger(): string
    {
        return $this->changeTrigger;
    }

    /**
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getSubjectType(): ?string
    {
        return $this->subjectType;
    }

    /**
     * @return int
     */
    public function getSubjectId(): ?int
    {
        return $this->subjectId;
    }

    #[ORM\PreUpdate]
    public function preUpdateTimestamp(): void
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    #[ORM\PrePersist]
    public function prePersistTimestamp(): void
    {
        $this->createdAt = $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return InventoryItem
     */
    public function getInventoryItem(): InventoryItem
    {
        return $this->inventoryItem;
    }

    /**
     * @return string|null
     */
    public function getWarehouseName(): ?string
    {
        return $this->warehouseName;
    }

    /**
     * @param string $warehouseName
     * @return $this
     */
    public function setWarehouseName(string $warehouseName)
    {
        $this->warehouseName = $warehouseName;

        return $this;
    }

    /**
     * @return string
     */
    public function getInventoryBatch(): ?string
    {
        return $this->inventoryBatch;
    }

    /**
     * @param string $inventoryBatch
     */
    public function setInventoryBatch(string $inventoryBatch): self
    {
        $this->inventoryBatch = $inventoryBatch;

        return $this;
    }
}
