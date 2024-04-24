<?php

namespace Marello\Bundle\InventoryBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;

use Marello\Bundle\InventoryBundle\Entity\InventoryItem;

class ExpectedInventoryItemController extends AbstractController
{
    /**
     * @param InventoryItem $inventoryItem
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_inventory_expected_inventory_item_view')]
    #[Template('@MarelloInventory/ExpectedInventoryItem/view.html.twig')]
    #[Acl(id: 'marello_inventory_inventory_view', type: 'entity', class: InventoryItem::class, permission: 'VIEW')]
    public function viewAction(InventoryItem $inventoryItem)
    {
        return [
            'entity' => $inventoryItem,
        ];
    }
}
