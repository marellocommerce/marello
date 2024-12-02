<?php

namespace Marello\Bundle\InventoryBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

use Marello\Bundle\InventoryBundle\Entity\BalancedInventoryLevel;
use Marello\Bundle\InventoryBundle\Async\Topic\ResolveRebalanceAllInventoryTopic;

class BalancedInventoryLevelController extends AbstractController
{
    /**
     * @return array
     */
    #[Route(path: '/', name: 'marello_inventory_balancedinventorylevel_index')]
    #[AclAncestor('marello_inventory_inventory_view')]
    #[Template('@MarelloInventory/BalancedInventoryLevel/index.html.twig')]
    public function indexAction()
    {
        return [
            'entity_class' => BalancedInventoryLevel::class,
        ];
    }

    /**
     * @param Request $request
     */
    #[Route(path: '/recalculate', name: 'marello_inventory_balancedinventorylevel_recalculate')]
    #[Acl(
        id: 'marello_inventory_inventory_recalculate_update',
        type: 'entity',
        class: BalancedInventoryLevel::class,
        permission: 'EDIT'
    )]
    public function recalculateAction(Request $request)
    {
        $messageProducer = $this->container->get(MessageProducerInterface::class);
        $messageProducer->send(
            ResolveRebalanceAllInventoryTopic::getName(),
            []
        );

        $request->getSession()->getFlashBag()->add(
            'success',
            $this->container
                ->get(TranslatorInterface::class)
                ->trans('marello.inventory.messages.success.inventory_rebalance.started')
        );
        return $this->redirectToRoute('marello_inventory_balancedinventorylevel_index');
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                MessageProducerInterface::class,
                TranslatorInterface::class,
            ]
        );
    }
}
