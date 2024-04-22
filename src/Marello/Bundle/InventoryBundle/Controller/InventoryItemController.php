<?php

namespace Marello\Bundle\InventoryBundle\Controller;

use Marello\Bundle\InventoryBundle\Entity\InventoryItem;
use Marello\Bundle\InventoryBundle\Form\Type\InventoryItemType;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventoryItemController extends AbstractController
{
    /**
     * @Template("@MarelloInventory/Inventory/index.html.twig")
     * @AclAncestor("marello_inventory_inventory_view")
     */
    #[Route(path: '/', name: 'marello_inventory_inventory_index')]
    public function indexAction()
    {
        return [
            'entity_class' => InventoryItem::class,
        ];
    }

    /**
     * @Template("@MarelloInventory/Inventory/view.html.twig")
     * @Acl(
     *      id="marello_inventory_inventory_view",
     *      type="entity",
     *      class="MarelloInventoryBundle:InventoryItem",
     *      permission="VIEW"
     * )
     * @param InventoryItem $inventoryItem
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_inventory_inventory_view')]
    public function viewAction(InventoryItem $inventoryItem)
    {
        return [
            'entity' => $inventoryItem,
            'product' => $inventoryItem->getProduct()
        ];
    }

    /**
     * @Template("@MarelloInventory/Inventory/update.html.twig")
     * @Acl(
     *      id="marello_inventory_inventory_update",
     *      type="entity",
     *      class="MarelloInventoryBundle:InventoryItem",
     *      permission="EDIT"
     * )
     * @param InventoryItem $inventoryItem
     * @param Request $request
     * @return array|RedirectResponse
     */
    #[Route(path: '/update/{id}', requirements: ['id' => '\d+'], name: 'marello_inventory_inventory_update')]
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
     * @Template("@MarelloInventory/Inventory/widget/info.html.twig")
     *
     * @param InventoryItem $inventoryItem
     * @return array
     */
    #[Route(path: '/widget/info/{id}', name: 'marello_inventory_widget_info', requirements: ['id' => '\d+'])]
    public function infoAction(InventoryItem $inventoryItem)
    {
        return [
            'item' => $inventoryItem,
            'product' => $inventoryItem->getProduct()
        ];
    }

    /**
     * @deprecated datagrid is now rendered inside view instead of separate widget
     * @Template("@MarelloInventory/Inventory/widget/datagrid.html.twig")
     *
     * @param InventoryItem $inventoryItem
     * @return array
     */
    #[Route(path: '/widget/datagrid/{id}', name: 'marello_inventory_widget_datagrid', requirements: ['id' => '\d+'])]
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
