<?php

namespace Marello\Bundle\InventoryBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use Marello\Bundle\InventoryBundle\Entity\DeliveryPromise;
use Marello\Bundle\InventoryBundle\Form\Type\DeliveryPromiseType;
use Marello\Bundle\InventoryBundle\Form\Handler\DeliveryPromiseHandler;

class DeliveryPromiseController extends AbstractController
{
    #[Route(path: '/', name: 'marello_inventory_deliverypromise_index')]
    #[Template('@MarelloInventory/DeliveryPromise/index.html.twig')]
    #[AclAncestor('marello_inventory_deliverypromise_view')]
    public function indexAction()
    {
        return [
            'entity_class' => DeliveryPromise::class
        ];
    }

    /**
     * @param DeliveryPromise $deliveryPromise
     * @return DeliveryPromise[]
     */
    #[Route(path: '/view/{id}', name: 'marello_inventory_deliverypromise_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[AclAncestor('marello_inventory_deliverypromise_view')]
    public function viewAction(DeliveryPromise $deliveryPromise)
    {
        return ['entity' => $deliveryPromise];
    }

    /**
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', name: 'marello_inventory_deliverypromise_create', methods: ['GET', 'POST'])]
    #[Template('@MarelloInventory/DeliveryPromise/update.html.twig')]
    #[Acl(id: 'marello_inventory_deliverypromise_create', type: 'entity', class: DeliveryPromise::class, permission: 'CREATE')]
    public function createAction(Request $request)
    {
        return $this->update($request);
    }

    /**
     * @param DeliveryPromise $deliveryPromise
     * @param Request $request
     * @return array
     */
    #[Route(
        path: '/update/{id}',
        name: 'marello_inventory_deliverypromise_update',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST']
    )]
    #[Template]
    #[Acl(id: 'marello_inventory_deliverypromise_update', type: 'entity', class: DeliveryPromise::class, permission: 'EDIT')]
    public function updateAction(DeliveryPromise $deliveryPromise, Request $request)
    {
        return $this->update($request, $deliveryPromise);
    }

    /**
     * Handles supplier updates and creation.
     *
     * @param Request $request
     * @param DeliveryPromise $deliveryPromise
     * @return array
     */
    protected function update(Request $request, DeliveryPromise $deliveryPromise = null)
    {
        if (!$deliveryPromise) {
            $deliveryPromise = new DeliveryPromise();
        }

        return $this->container->get(UpdateHandlerFacade::class)->update(
            $deliveryPromise,
            $this->createForm(DeliveryPromiseType::class, $deliveryPromise),
            $this->container
                ->get(TranslatorInterface::class)
                ->trans('marello.inventory.messages.success.deliverypromise.saved'),
            $request,
            $this->container->get(DeliveryPromiseHandler::class)
        );
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                UpdateHandlerFacade::class,
                TranslatorInterface::class,
                DeliveryPromiseHandler::class
            ]
        );
    }
}
