<?php

namespace Marello\Bundle\InventoryBundle\Controller;

use Marello\Bundle\OrderBundle\Entity\Order;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Marello\Bundle\InventoryBundle\Entity\Allocation;

class AllocationController extends AbstractController
{
    /**
     * @Template("@MarelloInventory/Allocation/index.html.twig")
     * @AclAncestor("marello_inventory_inventory_view")
     */
    #[Route(path: '/', name: 'marello_inventory_allocation_index')]
    public function indexAction()
    {
        return [
            'entity_class' => Allocation::class
        ];
    }

    /**
     * @Template("@MarelloInventory/Allocation/view.html.twig")
     * @AclAncestor("marello_inventory_inventory_view")
     *
     * @param Allocation $allocation
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_inventory_allocation_view')]
    public function viewAction(Allocation $allocation)
    {
        return ['entity' => $allocation];
    }

    /**
     * @AclAncestor("marello_inventory_inventory_view")
     * @Template("@MarelloInventory/Allocation/widget/orderAllocations.html.twig")
     * @param Request $request
     * @param Order $order
     * @return array
     */
    #[Route(path: '/widget/datagrid/{id}', name: 'marello_inventory_allocation_widget', requirements: ['id' => '\d+'])]
    public function allocationGridsAction(Request $request, Order $order)
    {
        $entityType = $request->get('entityType');
        $entityState = $request->get('entityState');

        return [
            'entity' => $order,
            'entityState' => $entityState,
            'entityType' => $entityType
        ];
    }
}
