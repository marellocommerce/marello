<?php

namespace Marello\Bundle\TicketBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;

use Marello\Bundle\TicketBundle\Entity\Ticket;
use Marello\Bundle\TicketBundle\Form\Type\TicketType;
use Marello\Bundle\TicketBundle\Provider\TicketStatusInterface;
use Marello\Bundle\TicketBundle\Entity\Repository\TicketRepository;

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

    #[Route(
        path: '/widget/sidebar-assigned-tickets/{perPage}',
        name: 'marello_ticket_widget_sidebar_assigned_tickets',
        requirements: ['perPage' => '\d+'],
        defaults: ['perPage' => 10],
        methods: ['GET', 'POST']
    )]
    #[Template('@MarelloTicket/Ticket/widget/assignedTicketsWidget.html.twig')]
    #[AclAncestor('marello_ticket_ticket_view')]
    public function ticketsWidgetAction(Request $request, int $perPage): array
    {
        /** @var TicketRepository $repository */
        $repository = $this->container->get(DoctrineHelper::class)->getEntityRepositoryForClass(Ticket::class);
        /** @var User $user */
        $user = $this->getUser();
        $statuses = $this->extractStatuses($request);
        $tickets = $repository->getTicketsAssignedTo($user, $perPage, $statuses);

        return [
            'tickets' => $tickets
        ];
    }

    protected function extractStatuses(Request $request): array
    {
        $possibleStatuses = [
            TicketStatusInterface::TICKET_STATUS_OPEN,
            TicketStatusInterface::TICKET_STATUS_IN_PROGRESS,
            TicketStatusInterface::TICKET_STATUS_RESOLVED,
            TicketStatusInterface::TICKET_STATUS_CLOSED,
        ];
        $statuses = $request->get('statuses', []);
        if ($statuses) {
            $statuses = array_keys($statuses);
            foreach ($statuses as $key => $status) {
                if (!\in_array($status, $possibleStatuses)) {
                    unset($statuses[$key]);
                }
            }
        }

        return $statuses;
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                UpdateHandlerFacade::class,
                DoctrineHelper::class
            ]
        );
    }
}
