<?php

namespace Marello\Bundle\OrderBundle\Migrations\Schema\v3_1_10;

use Doctrine\DBAL\Schema\Schema;

use Doctrine\DBAL\Schema\Table;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\OrderedMigrationInterface;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigEntityValueQuery;

use Marello\Bundle\OrderBundle\Migrations\Schema\v3_1_6\UpdateEntityConfigExtendClassQuery;

class FixOrderBundleMigrationsPart2 implements Migration, OrderedMigrationInterface
{
    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $orderTable = $schema->getTable('marello_order_order');
        $this->createUserOwnership($schema, $queries, $orderTable, Order::class);
        $queries->addPreQuery(
            new UpdateEntityConfigExtendClassQuery()
        );

        $orderItemTable = $schema->getTable('marello_order_order_item');
        $this->createUserOwnership($schema, $queries, $orderItemTable, OrderItem::class);
        $queries->addQuery(
            new UpdateEntityConfigEntityValueQuery(
                OrderItem::class,
                'security',
                'type',
                'ACL'
            )
        );
        $queries->addQuery(
            new UpdateEntityConfigEntityValueQuery(
                OrderItem::class,
                'security',
                'group_name',
                ''
            )
        );
    }

    /**
     * @param Schema $schema
     * @param QueryBag $queries
     * @param Table $table
     * @param string $entity
     * @return void
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function createUserOwnership(Schema $schema, QueryBag $queries, Table $table, string $entity): void
    {
        if ($table->hasColumn('user_owner_id')) {
            if (!$table->hasIndex('idx_a619dd649eb185f9')) {
                $table->addIndex(['user_owner_id']);
            }

            $table->addForeignKeyConstraint(
                $schema->getTable('oro_user'),
                ['user_owner_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );

            $queries->addQuery(
                new UpdateEntityConfigEntityValueQuery(
                    $entity,
                    'ownership',
                    'owner_type',
                    'USER'
                )
            );
            $queries->addQuery(
                new UpdateEntityConfigEntityValueQuery(
                    $entity,
                    'ownership',
                    'owner_field_name',
                    'owner'
                )
            );
            $queries->addQuery(
                new UpdateEntityConfigEntityValueQuery(
                    $entity,
                    'ownership',
                    'owner_column_name',
                    'user_owner_id'
                )
            );
            $queries->addQuery(
                new UpdateEntityConfigEntityValueQuery(
                    $entity,
                    'ownership',
                    'organization_field_name',
                    'organization'
                )
            );
            $queries->addQuery(
                new UpdateEntityConfigEntityValueQuery(
                    $entity,
                    'ownership',
                    'organization_column_name',
                    'organization_id'
                )
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getOrder()
    {
        return 20;
    }
}
