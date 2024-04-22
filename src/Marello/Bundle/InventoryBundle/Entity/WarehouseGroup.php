<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

/**
 * @Oro\Config(
 *  defaultValues={
 *       "security"={
 *           "type"="ACL",
 *           "group_name"=""
 *       },
 *      "ownership"={
 *          "owner_type"="ORGANIZATION",
 *          "owner_field_name"="organization",
 *          "owner_column_name"="organization_id"
 *      },
 *      "dataaudit"={
 *          "auditable"=true
 *      }
 *  }
 * )
 */
#[ORM\Table(name: 'marello_inventory_wh_group')]
#[ORM\Entity(repositoryClass: \Marello\Bundle\InventoryBundle\Entity\Repository\WarehouseGroupRepository::class)]
#[ORM\HasLifecycleCallbacks]
class WarehouseGroup implements OrganizationAwareInterface, ExtendEntityInterface
{
    use EntityCreatedUpdatedAtTrait;
    use AuditableOrganizationAwareTrait;
    use ExtendEntityTrait;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer', name: 'id')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected $id;

    /**
     * @var string
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "order"=10,
     *              "identity"=true
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    protected $name;

    /**
     * @var string
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "order"=20
     *          }
     *      }
     * )
     */
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    protected $description;

    /**
     * @var bool
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          },
     *          "importexport"={
     *              "order"=30
     *          }
     *      }
     *  )
     */
    #[ORM\Column(name: 'is_system', type: 'boolean', nullable: false, options: ['default' => false])]
    protected $system = false;

    /**
     * @var Warehouse[]
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     *  )
     */
    #[ORM\OneToMany(targetEntity: \Warehouse::class, mappedBy: 'group', cascade: ['persist'], fetch: 'EAGER')]
    protected $warehouses;

    /**
     * @var WarehouseChannelGroupLink
     *
     * @Oro\ConfigField(
     *      defaultValues={
     *          "dataaudit"={
     *              "auditable"=true
     *          }
     *      }
     *  )
     */
    #[ORM\OneToOne(targetEntity: \WarehouseChannelGroupLink::class, mappedBy: 'warehouseGroup')]
    protected $warehouseChannelGroupLink;

    public function __construct()
    {
        $this->warehouses = new ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSystem()
    {
        return $this->system;
    }

    /**
     * @param boolean $system
     * @return $this
     */
    public function setSystem($system)
    {
        $this->system = $system;

        return $this;
    }

    /**
     * @return Collection|Warehouse[]
     */
    public function getWarehouses()
    {
        return $this->warehouses;
    }

    /**
     * @param Warehouse $warehouse
     * @return $this
     */
    public function addWarehouse(Warehouse $warehouse)
    {
        if (!$this->warehouses->contains($warehouse)) {
            $warehouse->setGroup($this);
            $this->warehouses->add($warehouse);
        }

        return $this;
    }

    /**
     * @param Warehouse $warehouse
     * @return $this
     */
    public function removeWarehouse(Warehouse $warehouse)
    {
        if ($this->warehouses->contains($warehouse)) {
            $this->warehouses->removeElement($warehouse);
        }

        return $this;
    }

    /**
     * @return WarehouseChannelGroupLink
     */
    public function getWarehouseChannelGroupLink()
    {
        return $this->warehouseChannelGroupLink;
    }

    /**
     * @param WarehouseChannelGroupLink $warehouseChannelGroupLink
     * @return $this
     */
    public function setWarehouseChannelGroupLink($warehouseChannelGroupLink)
    {
        $this->warehouseChannelGroupLink = $warehouseChannelGroupLink;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}
