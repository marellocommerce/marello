<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema\v1_7_3;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;

class MarelloCustomerBundle implements Migration, ActivityExtensionAwareInterface
{
    /**
     * @var ActivityExtension
     */
    protected $activityExtension;

    /**
     * @inheritDoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createMarelloCustomerRoleTable($schema);
        $this->createMarelloCustomerAccessCustomerRoleTable($schema);

        $this->addMarelloCustomerRoleForeignKeys($schema);
        $this->addMarelloCustomerAccessCustomerRoleForeignKeys($schema);
    }

    /**
     * Create marello_customer_role table
     */
    protected function createMarelloCustomerRoleTable(Schema $schema): void
    {
        if (!$schema->hasTable('marello_customer_role')) {
            $table = $schema->createTable('marello_customer_role');
            $table->addColumn('id', 'integer', ['autoincrement' => true]);
            $table->addColumn('organization_id', 'integer', ['notnull' => false]);
            $table->addColumn('company_id', 'integer', ['notnull' => false]);
            $table->addColumn('role', 'string', ['length' => 255]);
            $table->addColumn('label', 'string', ['length' => 255]);
            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['role']);
            $table->addUniqueIndex(['organization_id', 'company_id', 'label']);

            $this->activityExtension->addActivityAssociation($schema, 'oro_note', 'marello_customer_role');
        }
    }

    /**
     * Create marello_customer_access_role table
     */
    protected function createMarelloCustomerAccessCustomerRoleTable(Schema $schema): void
    {
        if (!$schema->hasTable('marello_customer_access_role')) {
            $table = $schema->createTable('marello_customer_access_role');
            $table->addColumn('customer_id', 'integer');
            $table->addColumn('customer_role_id', 'integer');
            $table->setPrimaryKey(['customer_id', 'customer_role_id']);
        }
    }

    /**
     * Add marello_customer_access_role foreign keys.
     */
    protected function addMarelloCustomerAccessCustomerRoleForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('marello_customer_access_role');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_customer_role'),
            ['customer_role_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_customer_customer'),
            ['customer_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Add marello_customer_role foreign keys.
     */
    protected function addMarelloCustomerRoleForeignKeys(Schema $schema): void
    {
        $table = $schema->getTable('marello_customer_role');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_customer_company'),
            ['company_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * Sets the ActivityExtension
     *
     * @param ActivityExtension $activityExtension
     */
    public function setActivityExtension(ActivityExtension $activityExtension)
    {
        $this->activityExtension = $activityExtension;
    }
}
