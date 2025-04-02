<?php

namespace Marello\Bundle\TicketBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;

use Marello\Bundle\TicketBundle\Migrations\Schema\MarelloTicketBundleInstaller;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtension;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareInterface;

class MarelloTicketBundle implements Migration, AttachmentExtensionAwareInterface
{
    const MAX_FILE_SIZE_IN_MB = 5;

    /**
     * @var AttachmentExtension
     */
    protected $attachmentExtension;

    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateTicketTable($schema);
    }

    protected function updateTicketTable(Schema $schema)
    {
        $this->attachmentExtension->addFileRelation(
            $schema,
            'marello_ticket_ticket',
            'ticketAttachment',
            [
                'importexport' => ['excluded' => true],
                'extend' => ['owner' => ExtendScope::OWNER_SYSTEM],
                'attachment' => [
                    'mimetypes' => implode(',', MarelloTicketBundleInstaller::MIME_TYPES),
                    'acl_protected' => false
                ]
            ],
            self::MAX_FILE_SIZE_IN_MB
        );
    }

    public function setAttachmentExtension(AttachmentExtension $attachmentExtension)
    {
        $this->attachmentExtension = $attachmentExtension;
    }
}
