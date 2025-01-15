<?php

namespace Marello\Bundle\SalesBundle\Migrations\Schema\v1_5_1;

use Doctrine\DBAL\Types\Types;

use Psr\Log\LoggerInterface;

use Oro\Bundle\MigrationBundle\Migration\ParametrizedMigrationQuery;

use Marello\Bundle\SalesBundle\Entity\SalesChannel;

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
        $this->updateEntityConfig($logger);
    }

    /**
     * @param LoggerInterface $logger
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function updateEntityConfig(LoggerInterface $logger)
    {
        $entityName = SalesChannel::class;

        $sql = 'SELECT id, data FROM oro_entity_config WHERE class_name = ? LIMIT 1';
        $parameters = [$entityName];
        $row = $this->connection->fetchAssoc($sql, $parameters);
        $this->logQuery($logger, $sql, $parameters);
        $id = $row['id'];
        $data = isset($row['data']) ? $this->connection->convertToPHPValue($row['data'], Types::ARRAY) : [];
        if (isset($data['ownership']['owner_field_name'])) {
            $data['ownership']['owner_field_name'] = 'organization';
            $data = $this->connection->convertToDatabaseValue($data, Types::ARRAY);

            $sql = 'UPDATE oro_entity_config SET data = ? WHERE id = ?';
            $parameters = [$data, $id];
            $statement = $this->connection->prepare($sql);
            $statement->execute($parameters);
            $this->logQuery($logger, $sql, $parameters);
        }
        if (isset($data['ownership']['owner_column_name'])) {
            $data['ownership']['owner_column_name'] = 'organization_id';
            $data = $this->connection->convertToDatabaseValue($data, Types::ARRAY);

            $sql = 'UPDATE oro_entity_config SET data = ? WHERE id = ?';
            $parameters = [$data, $id];
            $statement = $this->connection->prepare($sql);
            $statement->execute($parameters);
            $this->logQuery($logger, $sql, $parameters);
        }
    }
}
