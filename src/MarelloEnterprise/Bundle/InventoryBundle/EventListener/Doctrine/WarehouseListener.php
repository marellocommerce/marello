<?php

namespace MarelloEnterprise\Bundle\InventoryBundle\EventListener\Doctrine;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Marello\Bundle\InventoryBundle\Entity\InventoryLevel;
use Marello\Bundle\InventoryBundle\Entity\Warehouse;
use Marello\Bundle\InventoryBundle\Entity\WarehouseGroup;
use Marello\Bundle\InventoryBundle\Provider\WarehouseTypeProviderInterface;
use Oro\Bundle\DistributionBundle\Handler\ApplicationState;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;

class WarehouseListener
{
    public function __construct(
        protected ApplicationState $applicationState,
        protected TranslatorInterface $translator,
        protected Session $session,
        protected AclHelper $aclHelper
    ) {
    }

    /**
     * @param Warehouse $warehouse
     * @param LifecycleEventArgs $args
     */
    public function prePersist(Warehouse $warehouse, LifecycleEventArgs $args)
    {
        if ($this->applicationState->isInstalled() && !$warehouse->getGroup()) {
            $em = $args->getObjectManager();
            $whType = $warehouse->getWarehouseType();
            if ($whType && $whType->getName() === WarehouseTypeProviderInterface::WAREHOUSE_TYPE_FIXED) {
                $group = new WarehouseGroup();
                $group
                    ->setName($warehouse->getLabel())
                    ->setOrganization($warehouse->getOwner())
                    ->setDescription(sprintf('%s group', $warehouse->getLabel()))
                    ->setSystem(false);
                $em->persist($group);
                $em->flush($group);
            } else {
                $group = $em
                    ->getRepository(WarehouseGroup::class)
                    ->findSystemWarehouseGroup($this->aclHelper);
            }

            if ($group) {
                $warehouse->setGroup($group);
            }
        }
    }

    /**
     * @param Warehouse $warehouse
     * @param LifecycleEventArgs $args
     */
    public function preRemove(Warehouse $warehouse, LifecycleEventArgs $args)
    {
        if ($warehouse->isDefault()) {
            $message = $this->translator->trans(
                'marelloenterprise.inventory.messages.error.warehouse.default_warehouse_deletion'
            );
        }
        $inventoryLevels = $args
            ->getObjectManager()
            ->getRepository(InventoryLevel::class)
            ->findBy(['warehouse' => $warehouse]);
        if (!empty($inventoryLevels)) {
            $message = $this->translator->trans(
                'marelloenterprise.inventory.messages.error.warehouse.warehouse_with_inventory_deletion'
            );
        }
        if (isset($message)) {
            $this->session->getFlashBag()->add('error', $message);
            throw new AccessDeniedException($message);
        }
        if ($group = $warehouse->getGroup()) {
            if (!$group->isSystem() && $group->getWarehouses()->count() < 1) {
                $args->getObjectManager()->remove($group);
            }
        }
    }
}
