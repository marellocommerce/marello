<?php

namespace Marello\Bundle\TicketBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;

use Marello\Bundle\TicketBundle\Entity\Ticket;
use Oro\Bundle\OrganizationBundle\Entity\Organization;

class UpdateCurrentTicketsWithOrganization extends AbstractFixture
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->updateCurrentTickets();
    }

    /**
     * update current OrderItems with organization
     */
    public function updateCurrentTickets()
    {
        $tickets = $this->manager
            ->getRepository(Ticket::class)
            ->findBy(['organization' => null]);

        /** @var Ticket $ticket */
        foreach ($tickets as $ticket) {
            $organization = $ticket->getCustomer()?->getOrganization();

            if (!$organization) {
                $organization = $ticket->getOwner()?->getOrganization();
            }

            if (!$organization) {
                $organization = $ticket->getAssignedTo()?->getOrganization();
            }

            if (!$organization) {
                $organization = $this->manager
                    ->getRepository(Organization::class)
                    ->getFirst();
            }

            $ticket->setOrganization($organization);
            $this->manager->persist($ticket);
        }
        
        $this->manager->flush();
    }
}
