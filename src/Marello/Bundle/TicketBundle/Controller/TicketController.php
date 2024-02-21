<?php

namespace Marello\Bundle\TicketBundle\Controller;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Marello\Bundle\TicketBundle\Entity\Ticket;

class TicketController extends AbstractController
{
    /**
     * @Route("/", name="marello_ticket_index")
     * @Template
     * @AclAncestor("marello_ticket_view")
     */
    public function indexAction(): array
    {
        return [
            'entity_class' => Ticket::class
        ];
    }

    /**
     * @Route(path="/view/{id}", name="marello_ticket_view", requirements={"id"="\d+"})
     * @Template
     */
    public function viewAction(Ticket $ticket)
    {
        return [
            'entity' => $ticket
        ];
    }
}