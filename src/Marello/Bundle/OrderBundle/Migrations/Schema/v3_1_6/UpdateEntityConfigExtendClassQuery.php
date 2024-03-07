<?php

namespace Marello\Bundle\OrderBundle\Migrations\Schema\v3_1_6;

use Doctrine\DBAL\Types\Type;
use Marello\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\ActivityListBundle\Entity\ActivityList;
use Oro\Bundle\AttachmentBundle\Entity\Attachment;
use Oro\Bundle\EmailBundle\Entity\Email;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedMigrationQuery;
use Oro\Bundle\NoteBundle\Entity\Note;
use Psr\Log\LoggerInterface;

class UpdateEntityConfigExtendClassQuery extends ParametrizedMigrationQuery
{
    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Update entity extend class configuration on given entity';
    }

    /**
     * {@inheritdoc}
     */
    public function execute(LoggerInterface $logger)
    {
        $this->updateOrderEntityConfig($logger);
    }

    /**
     * @param LoggerInterface $logger
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function updateOrderEntityConfig(LoggerInterface $logger)
    {
        $sql = 'SELECT id FROM oro_entity_config WHERE class_name = ? LIMIT 1';
        $parameters = ['Marello\Bundle\OrderBundle\Entity\Order'];
        $row = $this->connection->fetchAssoc($sql, $parameters);
        $this->logQuery($logger, $sql, $parameters);

        $entityId = $row['id'];
        $fieldName = 'shipment';
            $sql = 'DELETE FROM oro_entity_config_field WHERE entity_id = ? AND field_name = ?';
        $parameters = [$entityId, $fieldName];
        $statement = $this->connection->prepare($sql);
        $statement->execute($parameters);
        $this->logQuery($logger, $sql, $parameters);
    }
}
