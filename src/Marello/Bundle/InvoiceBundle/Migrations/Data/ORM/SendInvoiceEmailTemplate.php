<?php

namespace Marello\Bundle\InvoiceBundle\Migrations\Data\ORM;

use Oro\Bundle\EmailBundle\Migrations\Data\ORM\AbstractEmailFixture;

class SendInvoiceEmailTemplate extends AbstractEmailFixture
{
    public function getEmailsDir()
    {
        return __DIR__.'/data/email_templates/';
    }
}
