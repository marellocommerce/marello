<?php

namespace Marello\Bundle\TicketBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;

use Marello\Bundle\TicketBundle\Entity\Ticket;
use Marello\Bundle\TicketBundle\Form\Type\TicketType;

class TicketController extends AbstractController
{
    #[Route(path: '/', name: 'marello_ticket_ticket_index')]
    #[Template]
    #[AclAncestor('marello_ticket_ticket_view')]
    public function indexAction(): array
    {
        return ['entity_class' => Ticket::class];
    }

    #[Route(path: '/view/{id}', name: 'marello_ticket_ticket_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'marello_ticket_ticket_view', type: 'entity', class: Ticket::class, permission: 'VIEW')]
    public function viewAction(Ticket $ticket)
    {
        return ['entity' => $ticket];
    }

    #[Route(path: '/create', name: 'marello_ticket_ticket_create', methods: ['GET', 'POST'])]
    #[Template('@MarelloTicket/Ticket/update.html.twig')]
    #[Acl(id: 'marello_ticket_ticket_create', type: 'entity', class: Ticket::class, permission: 'CREATE')]
    public function createAction(Request $request): array|RedirectResponse
    {
        $createMessage = $this->container->get(TranslatorInterface::class)->trans(
            'marello.ticket.saved.message'
        );

        return $this->update(new Ticket(), $request, $createMessage);
    }

    #[Route(
        path: '/update/{id}',
        name: 'marello_ticket_ticket_update',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST']
    )]
    #[Template]
    #[Acl(id: 'marello_ticket_ticket_update', type: 'entity', class: Ticket::class, permission: 'EDIT')]
    public function updateAction(Ticket $entity, Request $request): array|RedirectResponse
    {
        $createMessage = $this->container->get(TranslatorInterface::class)->trans(
            'marello.ticket.saved.message'
        );

        return $this->update($entity, $request, $createMessage);
    }

    protected function update(
        Ticket $entity,
        Request $request,
        string $message = ''
    ): array|RedirectResponse {
        return $this->container->get(UpdateHandlerFacade::class)->update(
            $entity,
            $this->createForm(TicketType::class, $entity),
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
                TranslatorInterface::class,
                UpdateHandlerFacade::class,
            ]
        );
    }
}
