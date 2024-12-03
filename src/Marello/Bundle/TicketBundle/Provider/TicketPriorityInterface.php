<?php

namespace Marello\Bundle\TicketBundle\Provider;

interface TicketPriorityInterface
{
    const TICKET_PRIORITY_ENUM_CODE = 'marello_ticket_priority';
    const TICKET_PRIORITY_NORMAL = 'normal';
    const TICKET_PRIORITY_LOW = 'low';
    const TICKET_PRIORITY_HIGH = 'high';
}
