<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use Oro\Bundle\SecurityBundle\Tools\UUIDGenerator;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\CoreBundle\DerivedProperty\DerivedPropertyAwareInterface;

#[ORM\Table(name: 'marello_inventory_batch')]
#[ORM\UniqueConstraint(name: 'UNIQ_380BD44456B7924', columns: ['batch_number', 'inventory_level_id'])]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[Oro\Config(
    defaultValues: [
        'entity' => ['icon' => 'fa-cubes'],
        'security' => ['type' => 'ACL', 'group_name' => ''],
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true]
    ]
)]
class InventoryBatch implements
    DerivedPropertyAwareInterface,
    OrganizationAwareInterface,
    ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;
    
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(name: 'batch_number', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $batchNumber;

    /**
     * @var string
     */
    #[ORM\Column(name: 'batch_reference', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $batchReference;


    /**
     * @var string
     */
    #[ORM\Column(name: 'purchase_reference', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $purchaseReference;

    /**
     * @var int
     */
    #[ORM\Column(name: 'quantity', type: Types::INTEGER)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $quantity = 0;
    
    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'delivery_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $deliveryDate;
    
    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'expiration_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $expirationDate;

    /**
     * @var \DateTime
     */
    #[ORM\Column(name: 'sell_by_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $sellByDate;

    /**
     * @var int
     */
    #[ORM\Column(name: 'purchase_price', type: 'money', nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $purchasePrice;

    /**
     * @var int
     */
    #[ORM\Column(name: 'total_price', type: 'money', nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $totalPrice;
    
    /**
     * @var InventoryLevel
     */
    #[ORM\JoinColumn(name: 'inventory_level_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: InventoryLevel::class, cascade: ['persist'], inversedBy: 'inventoryBatches')]
    #[Oro\ConfigField(
        defaultValues: [
            'entity' => ['label' => 'marello.inventory.inventorylevel.entity_label'],
            'importexport' => ['excluded' => true],
            'dataaudit' => ['auditable' => false]
        ]
    )]
    protected $inventoryLevel;


    /**
     * @var string
     */
    #[ORM\Column(name: 'order_on_demand_ref', type: Types::STRING, nullable: true)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $orderOnDemandRef;

    public function __clone()
    {
        if ($this->id) {
            $this->id = null;
        }
        if ($this->inventoryLevel) {
            $this->inventoryLevel = null;
        }
    }
    
    /**
     * @param int $id
     */
    public function setDerivedProperty($id)
    {
        if (!$this->batchNumber) {
            $this->setBatchNumber(UUIDGenerator::v4());
        }
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('#%s', $this->batchNumber);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBatchNumber()
    {
        return $this->batchNumber;
    }

    /**
     * @param string $batchNumber
     * @return InventoryBatch
     */
    public function setBatchNumber($batchNumber)
    {
        $this->batchNumber = $batchNumber;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getBatchReference()
    {
        return $this->batchReference;
    }

    /**
     * @param string $batchReference
     * @return InventoryBatch
     */
    public function setBatchReference($batchReference)
    {
        $this->batchReference = $batchReference;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getPurchaseReference()
    {
        return $this->purchaseReference;
    }

    /**
     * @param string $purchaseReference
     * @return InventoryBatch
     */
    public function setPurchaseReference($purchaseReference)
    {
        $this->purchaseReference = $purchaseReference;
        
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return InventoryBatch
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    /**
     * @param \DateTime $deliveryDate
     * @return InventoryBatch
     */
    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTime $expirationDate
     * @return InventoryBatch
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
        
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSellByDate()
    {
        return $this->sellByDate;
    }

    /**
     * @param \DateTime $sellByDate
     * @return InventoryBatch
     */
    public function setSellByDate($sellByDate)
    {
        $this->sellByDate = $sellByDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getPurchasePrice()
    {
        return $this->purchasePrice;
    }

    /**
     * @param int $purchasePrice
     * @return InventoryBatch
     */
    public function setPurchasePrice($purchasePrice)
    {
        $this->purchasePrice = $purchasePrice;
        
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @param int $totalPrice
     * @return InventoryBatch
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
        
        return $this;
    }

    /**
     * @return InventoryLevel
     */
    public function getInventoryLevel()
    {
        return $this->inventoryLevel;
    }

    /**
     * @param InventoryLevel $inventoryLevel
     * @return InventoryBatch
     */
    public function setInventoryLevel(InventoryLevel $inventoryLevel)
    {
        $this->inventoryLevel = $inventoryLevel;
        
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrderOnDemandRef(): ?string
    {
        return $this->orderOnDemandRef;
    }

    /**
     * @param string|null $orderOnDemandRef
     * @return InventoryBatch
     */
    public function setOrderOnDemandRef(string $orderOnDemandRef = null): self
    {
        $this->orderOnDemandRef = $orderOnDemandRef;

        return $this;
    }
}
