<?php

namespace Marello\Bundle\InventoryBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Form\Type\InventoryItemType;

class InventoryItemController extends AbstractController
{
    #[Route(path: '/', name: 'marello_inventory_inventory_index')]
    #[Template('@MarelloInventory/Inventory/index.html.twig')]
    #[AclAncestor('marello_inventory_inventory_view')]
    public function indexAction()
    {
        return [
            'entity_class' => InventoryItem::class,
        ];
    }

    /**
     * @param InventoryItem $inventoryItem
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_inventory_inventory_view')]
    #[Template('@MarelloInventory/Inventory/view.html.twig')]
    #[Acl(id: 'marello_inventory_inventory_view', type: 'entity', class: InventoryItem::class, permission: 'VIEW')]
    public function viewAction(InventoryItem $inventoryItem)
    {
        return [
            'entity' => $inventoryItem,
            'product' => $inventoryItem->getProduct()
        ];
    }

    /**
     * @param InventoryItem $inventoryItem
     * @param Request $request
     * @return array|RedirectResponse
     */
    #[Route(path: '/update/{id}', requirements: ['id' => '\d+'], name: 'marello_inventory_inventory_update')]
    #[Template('@MarelloInventory/Inventory/update.html.twig')]
    #[Acl(id: 'marello_inventory_inventory_update', type: 'entity', class: InventoryItem::class, permission: 'EDIT')]
    public function updateAction(InventoryItem $inventoryItem, Request $request)
    {
        $result = $this->container->get(UpdateHandlerFacade::class)->update(
            $inventoryItem,
            $this->createForm(InventoryItemType::class, $inventoryItem),
            $this->container->get(TranslatorInterface::class)->trans('marello.inventory.messages.success.inventoryitem.saved'),
            $request
        );
        
        return $result;
    }

    /**
     *
     * @param InventoryItem $inventoryItem
     * @return array
     */
    #[Route(path: '/widget/info/{id}', name: 'marello_inventory_widget_info', requirements: ['id' => '\d+'])]
    #[Template('@MarelloInventory/Inventory/widget/info.html.twig')]
    public function infoAction(InventoryItem $inventoryItem)
    {
        return [
            'item' => $inventoryItem,
            'product' => $inventoryItem->getProduct()
        ];
    }

    /**
     * @deprecated datagrid is now rendered inside view instead of separate widget
     *
     * @param InventoryItem $inventoryItem
     * @return array
     */
    #[Route(path: '/widget/datagrid/{id}', name: 'marello_inventory_widget_datagrid', requirements: ['id' => '\d+'])]
    #[Template('@MarelloInventory/Inventory/widget/datagrid.html.twig')]
    public function datagridAction(InventoryItem $inventoryItem)
    {
        return [
            'item' => $inventoryItem,
        ];
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                UpdateHandlerFacade::class,
                TranslatorInterface::class,
            ]
        );
    }
}
