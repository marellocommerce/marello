<?php

namespace Marello\Bundle\TicketBundle\Provider;

interface TicketSourceInterface
{
    const TICKET_SOURCE_ENUM_CODE = 'marello_ticket_source';
    const TICKET_SOURCE_WEB = 'web';
    const TICKET_SOURCE_EMAIL = 'email';
    const TICKET_SOURCE_PHONE = 'phone';
    const TICKET_SOURCE_OTHER = 'other';
}
