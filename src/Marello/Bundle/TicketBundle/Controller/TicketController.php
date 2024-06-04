<?php

namespace Marello\Bundle\TicketBundle\Controller;

use Marello\Bundle\TicketBundle\Entity\Repository\TicketRepository;
use Marello\Bundle\TicketBundle\Entity\Ticket;
use Marello\Bundle\TicketBundle\Provider\TicketStatusInterface;
use Oro\Bundle\UserBundle\Entity\User;
use Marello\Bundle\TicketBundle\Form\Type\TicketType;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Marello\Bundle\TicketBundle\Entity\Ticket as TicketAlias;
use Symfony\Contracts\Translation\TranslatorInterface;

class TicketController extends AbstractController
{
    /**
     * @Route("/", name="marello_ticket_index")
     * @Template
     * @AclAncestor("marello_ticket_view")
     */
    public function indexAction(): array
    {
        return [ 'entity_class' => 'MarelloTicketBundle::Ticket' ];
    }

    /**
     * @Route(path="/view/{id}", name="marello_ticket_view", requirements={"id"="\d+"})
     * @Template
     */
    public function viewAction(TicketAlias $ticket)
    {
        return [
            'entity' => $ticket
        ];
    }

    /**
     * @Route(
     *     path="/create",
     *     name="marello_ticket_create",
     *     options={"expose"=true}
     * )
     * @Template("@MarelloTicket/Ticket/update.html.twig")
     * @Acl(
     *       id="marello_ticket_create",
     *       type="entity",
     *       class="MarelloTicketBundle:Ticket",
     *       permission="CREATE"
     *  )
     */
    public function createAction(Request $request): array|RedirectResponse
    {
        $createMessage = $this->get(TranslatorInterface::class)->trans(
            'marello.ticket.saved.message'
        );

        return $this->update(new Ticket(), $request, $createMessage);
    }

    /**
     * @Route(
     *     path="/update/{id}",
     *     name="marello_ticket_update",
     *     requirements={"id"="\d+"}
     * )
     * @Template
     * @Acl(
     *      id="marello_ticket_update",
     *      type="entity",
     *      class="MarelloTicketBundle:Ticket",
     *      permission="EDIT"
     * )
     */
    public function updateAction(Ticket $entity, Request $request): array|RedirectResponse
    {
        $createMessage = $this->get(TranslatorInterface::class)->trans(
            'marello.ticket.saved.message'
        );

        return $this->update($entity, $request, $createMessage);
    }

    protected function update(
        Ticket $entity,
        Request $request,
        string $message = ''
    ): array|RedirectResponse {
        return $this->get(UpdateHandlerFacade::class)->update(
            $entity,
            $this->createForm(TicketType::class, $entity),
            $message,
            $request,
            null
        );
    }

    /**
     * @Route(
     *     "/widget/sidebar-assigned-tickets/{perPage}",
     *     name="marello_ticket_widget_sidebar_assigned_tickets",
     *     defaults={"perPage" = 10},
     *     requirements={"perPage"="\d+"}
     * )
     * @AclAncestor("marello_ticket_view")
     */
    public function ticketsWidgetAction(Request $request, int $perPage): Response
    {
        /** @var TicketRepository $repository */
        $repository = $this->container->get('doctrine')->getRepository(Ticket::class);
        /** @var User $user */
        $user = $this->getUser();
        $statuses = $this->extractStatuses($request);
        $tickets = $repository->getTicketsAssignedTo($user, $perPage, $statuses);

        return $this->render(
            '@MarelloTicket/Ticket/widget/assignedTicketsWidget.html.twig',
            ['tickets' => $tickets]
        );
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

    public static function getSubscribedServices()
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