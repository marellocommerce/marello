<?php

namespace Marello\Bundle\InventoryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Marello\Bundle\CoreBundle\Model\EntityCreatedUpdatedAtTrait;
use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;
use Oro\Bundle\EntityConfigBundle\Metadata\Attribute as Oro;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityInterface;
use Oro\Bundle\EntityExtendBundle\Entity\ExtendEntityTrait;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\Ownership\AuditableOrganizationAwareTrait;

#[ORM\Entity(repositoryClass: \Marello\Bundle\InventoryBundle\Entity\Repository\WarehouseChannelGroupLinkRepository::class), ORM\HasLifecycleCallbacks]
#[ORM\Table(name: 'marello_inventory_wh_chg_link')]
#[Oro\Config(
    defaultValues: [
        'ownership' => [
            'owner_type' => 'ORGANIZATION',
            'owner_field_name' => 'organization',
            'owner_column_name' => 'organization_id'
        ],
        'dataaudit' => ['auditable' => true],
        'security' => ['type' => 'ACL', 'group_name' => '']
    ]
)]
class WarehouseChannelGroupLink implements OrganizationAwareInterface, ExtendEntityInterface
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
     * @var bool
     */
    #[ORM\Column(name: 'is_system', type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $system = false;

    /**
     * @var WarehouseGroup
     */
    #[ORM\JoinColumn(name: 'warehouse_group_id', referencedColumnName: 'id')]
    #[ORM\OneToOne(inversedBy: 'warehouseChannelGroupLink', targetEntity: WarehouseGroup::class)]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $warehouseGroup;

    /**
     * @var SalesChannelGroup[]
     */
    #[ORM\JoinTable(name: 'marello_inventory_lnk_join_chg')]
    #[ORM\JoinColumn(name: 'link_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'channel_group_id', referencedColumnName: 'id', unique: true)]
    #[ORM\ManyToMany(targetEntity: \Marello\Bundle\SalesBundle\Entity\SalesChannelGroup::class, fetch: 'EAGER')]
    #[Oro\ConfigField(defaultValues: ['dataaudit' => ['auditable' => true]])]
    protected $salesChannelGroups;

    public function __construct()
    {
        $this->salesChannelGroups = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return WarehouseGroup
     */
    public function getWarehouseGroup()
    {
        return $this->warehouseGroup;
    }

    /**
     * @param WarehouseGroup $warehouseGroup
     * @return $this
     */
    public function setWarehouseGroup(WarehouseGroup $warehouseGroup)
    {
        $this->warehouseGroup = $warehouseGroup;

        return $this;
    }

    /**
     * @return Collection|SalesChannelGroup[]
     */
    public function getSalesChannelGroups()
    {
        return $this->salesChannelGroups;
    }

    /**
     * @param SalesChannelGroup $salesChannelGroup
     * @return $this
     */
    public function addSalesChannelGroup(SalesChannelGroup $salesChannelGroup)
    {
        if (!$this->salesChannelGroups->contains($salesChannelGroup)) {
            $this->salesChannelGroups->add($salesChannelGroup);
        }

        return $this;
    }

    /**
     * @param SalesChannelGroup $salesChannelGroup
     * @return $this
     */
    public function removeSalesChannelGroup(SalesChannelGroup $salesChannelGroup)
    {
        if ($this->salesChannelGroups->contains($salesChannelGroup)) {
            $this->salesChannelGroups->removeElement($salesChannelGroup);
        }

        return $this;
    }
}
