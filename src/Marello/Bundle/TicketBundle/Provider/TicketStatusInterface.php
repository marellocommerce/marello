<?php

namespace Marello\Bundle\TicketBundle\Provider;

interface TicketStatusInterface
{
    const TICKET_STATUS_ENUM_CODE = 'marello_ticket_status';
    const TICKET_STATUS_OPEN = 'open';
    const TICKET_STATUS_IN_PROGRESS = 'in_progress';
    const TICKET_STATUS_RESOLVED = 'resolved';
    const TICKET_STATUS_CLOSED = 'closed';
}
