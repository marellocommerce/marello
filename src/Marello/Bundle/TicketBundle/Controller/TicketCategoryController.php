<?php

namespace Marello\Bundle\TicketBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;

use Marello\Bundle\TicketBundle\Entity\TicketCategory;
use Marello\Bundle\TicketBundle\Form\Type\TicketCategoryType;

class TicketCategoryController extends AbstractController
{
    #[Route(path: '/', name: 'marello_ticket_category_index')]
    #[Template]
    #[AclAncestor('marello_ticket_category_view')]
    public function indexAction()
    {
        return ['entity_class' => TicketCategory::class];
    }

    #[Route(path: '/view/{id}', name: 'marello_ticket_category_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'marello_ticket_ticket_view', type: 'entity', class: TicketCategory::class, permission: 'VIEW')]
    public function viewAction(TicketCategory $category)
    {
        return [
            'entity' => $category
        ];
    }

    #[Route(path: '/create', name: 'marello_ticket_category_create', methods: ['GET', 'POST'])]
    #[Template('@MarelloTicket/TicketCategory/update.html.twig')]
    #[Acl(id: 'marello_ticket_category_create', type: 'entity', class: TicketCategory::class, permission: 'CREATE')]
    public function createAction(Request $request): array|RedirectResponse
    {
        $createMessage = $this->container->get(TranslatorInterface::class)->trans(
            'marello.ticket.category.saved.message'
        );

        return $this->update(new TicketCategory(), $request, $createMessage);
    }

    #[Route(
        path: '/update/{id}',
        name: 'marello_ticket_category_update',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST']
    )]
    #[Template]
    #[Acl(id: 'marello_ticket_category_update', type: 'entity', class: TicketCategory::class, permission: 'EDIT')]
    public function updateAction(TicketCategory $entity, Request $request): array|RedirectResponse
    {
        $createMessage = $this->container->get(TranslatorInterface::class)->trans(
            'marello.ticket.category.saved.message'
        );

        return $this->update($entity, $request, $createMessage);
    }

    protected function update(
        TicketCategory $entity,
        Request        $request,
        string         $message = ''
    ): array|RedirectResponse {
        return $this->container->get(UpdateHandlerFacade::class)->update(
            $entity,
            $this->createForm(TicketCategoryType::class, $entity),
            $message,
            $request,
            null
        );
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
