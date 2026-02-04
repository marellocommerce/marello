<?php

namespace Marello\Bundle\CustomerBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtension;
use Oro\Bundle\ActivityBundle\Migration\Extension\ActivityExtensionAwareInterface;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtension;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class MarelloCustomerBundleInstaller implements
    Installation,
    ActivityExtensionAwareInterface,
    AttachmentExtensionAwareInterface
{
    const MARELLO_CUSTOMER_TABLE = 'marello_customer_customer';
    const MARELLO_COMPANY_TABLE = 'marello_customer_company';
    const MARELLO_COMPANY_JOIN_ADDRESS_TABLE = 'marello_company_join_address';
    const MARELLO_CUSTOMER_GROUP_TABLE = 'marello_customer_group';

    /**
     * @var ActivityExtension
     */
    protected $activityExtension;

    /**
     * @var AttachmentExtension
     */
    protected $attachmentExtension;

    /**
     * @inheritDoc
     */
    public function getMigrationVersion()
    {
        return 'v1_7_6';
    }

    /**
     * @inheritDoc
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->createMarelloCompanyTable($schema);
        $this->createMarelloCompanyJoinAddressTable($schema);
        $this->createMarelloCustomerTable($schema);
        $this->createMarelloCustomerRoleTable($schema);
        $this->createMarelloCustomerAccessCustomerRoleTable($schema);
        $this->createMarelloCustomerGroupTable($schema);

        $this->addMarelloCompanyForeignKeys($schema);
        $this->addMarelloCompanyJoinAddressForeignKeys($schema);
        $this->addMarelloCustomerForeignKeys($schema);
        $this->addMarelloCustomerRoleForeignKeys($schema);
        $this->addMarelloCustomerAccessCustomerRoleForeignKeys($schema);
        $this->addMarelloAddressForeignKeys($schema);
        $this->addMarelloCustomerOwnerToOroEmailAddress($schema);
    }

    /**
     * Create marello_customer_company table
     *
     * @param Schema $schema
     */
    protected function createMarelloCompanyTable(Schema $schema)
    {
        $table = $schema->createTable(self::MARELLO_COMPANY_TABLE);

        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('company_number', 'string', ['length' => 255, 'notnull' => false]);
        $table->addColumn('payment_term_id', 'integer', ['notnull' => false]);
        $table->addColumn('tax_identification_number', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('parent_id', 'integer', ['notnull' => false]);
        $table->addColumn('sales_rep_id', 'integer', ['notnull' => false]);
        $table->addColumn('fallback_sales_rep_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('discount_percentage', 'float', ['notnull' => false]);
        $table->addColumn('invoice_email', 'string', ['notnull' => false]);
        $table->addColumn('send_copy_to_customer', 'boolean', ['notnull' => true, 'default' => false]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['company_number', 'organization_id'], 'marello_customer_company_compnrorgidx');
    }

    /**
     * @param Schema $schema
     */
    protected function createMarelloCompanyJoinAddressTable(Schema $schema)
    {
        $table = $schema->createTable(self::MARELLO_COMPANY_JOIN_ADDRESS_TABLE);
        $table->addColumn('company_id', 'integer', ['notnull' => true]);
        $table->addColumn('address_id', 'integer', ['notnull' => true]);
        $table->addUniqueIndex(['address_id'], 'UNIQ_629E2BBEA750E85234');
        $table->setPrimaryKey(['company_id', 'address_id']);
    }

    /**
     * @param Schema $schema
     */
    protected function createMarelloCustomerTable(Schema $schema)
    {
        $table = $schema->createTable(self::MARELLO_CUSTOMER_TABLE);
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('primary_address_id', 'integer', ['notnull' => false]);
        $table->addColumn('shipping_address_id', 'integer', ['notnull' => false]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime');
        $table->addColumn('name_prefix', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('first_name', 'string', ['length' => 255]);
        $table->addColumn('middle_name', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('last_name', 'string', ['length' => 255]);
        $table->addColumn('name_suffix', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('email', 'string', ['length' => 255]);
        $table->addColumn('email_lowercase', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('is_hidden', 'boolean', ['notnull' => false, 'default' => false]);
        $table->addColumn('company_id', 'integer', ['notnull' => false]);
        $table->addColumn('customer_number', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('customer_group_id', 'integer', ['notnull' => false]);
        $table->addColumn('enabled', 'boolean', ['notnull' => false, 'default' => false]);
        $table->addColumn('confirmed', 'boolean', ['notnull' => false, 'default' => false]);
        $table->addColumn('username', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('salt', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('password', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('confirmation_token', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('password_requested', 'datetime', ['notnull' => false]);
        $table->addColumn('password_changed', 'datetime', ['notnull' => false]);
        $table->addColumn('last_login', 'datetime', ['notnull' => false]);
        $table->addColumn('login_count', 'integer', ['default' => '0', 'unsigned' => true]);
        $table->addColumn('localization_id', 'integer', ['notnull' => false]);
        $table->addColumn('data', 'json', ['notnull' => false, 'comment' => '(DC2Type:json)']);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['organization_id']);
        $table->addIndex(['primary_address_id']);
        $table->addIndex(['shipping_address_id']);
        $table->addUniqueIndex(['email', 'organization_id'], 'marello_customer_emailorgidx');
        $table->addUniqueIndex(['customer_number', 'organization_id'], 'marello_customer_numberorgidx');

        $this->attachmentExtension->addAttachmentAssociation($schema, $table->getName());
        $this->activityExtension->addActivityAssociation($schema, 'oro_note', $table->getName());
        $this->activityExtension->addActivityAssociation($schema, 'oro_email', $table->getName());
    }

    /**
     * Create marello_customer_role table
     */
    protected function createMarelloCustomerRoleTable(Schema $schema): void
    {
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

    /**
     * Create marello_customer_access_role table
     */
    protected function createMarelloCustomerAccessCustomerRoleTable(Schema $schema): void
    {
        $table = $schema->createTable('marello_customer_access_role');
        $table->addColumn('customer_id', 'integer');
        $table->addColumn('customer_role_id', 'integer');
        $table->setPrimaryKey(['customer_id', 'customer_role_id']);
    }

    /**
     * @param Schema $schema
     */
    protected function createMarelloCustomerGroupTable(Schema $schema)
    {
        $table = $schema->createTable(self::MARELLO_CUSTOMER_GROUP_TABLE);
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'string', ['length' => 255]);
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Add oro_customer foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloCompanyForeignKeys(Schema $schema)
    {
        $table = $schema->getTable(static::MARELLO_COMPANY_TABLE);
        $table->addForeignKeyConstraint(
            $table,
            ['parent_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_payment_term'),
            ['payment_term_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['sales_rep_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['fallback_sales_rep_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * @param Schema $schema
     */
    protected function addMarelloCompanyJoinAddressForeignKeys(Schema $schema)
    {
        $table = $schema->getTable(self::MARELLO_COMPANY_JOIN_ADDRESS_TABLE);
        $table->addForeignKeyConstraint(
            $schema->getTable(self::MARELLO_COMPANY_TABLE),
            ['company_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_typed_address'),
            ['address_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
    }

    /**
     * @param Schema $schema
     */
    protected function addMarelloCustomerForeignKeys(Schema $schema)
    {
        $table = $schema->getTable(self::MARELLO_CUSTOMER_TABLE);
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_address'),
            ['primary_address_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_address'),
            ['shipping_address_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable(self::MARELLO_COMPANY_TABLE),
            ['company_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable(self::MARELLO_CUSTOMER_GROUP_TABLE),
            ['customer_group_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_localization'),
            ['localization_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
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
     * Add marello_address foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloAddressForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_address');
        $table->addForeignKeyConstraint(
            $schema->getTable(self::MARELLO_CUSTOMER_TABLE),
            ['customer_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
    }

    /**
     * Add owner_marello_customer_id to oro_email_address table.
     *
     * @param Schema $schema
     */
    protected function addMarelloCustomerOwnerToOroEmailAddress(Schema $schema)
    {
        $table = $schema->getTable('oro_email_address');
        $table->addColumn('owner_marello_customer_id', 'integer', ['notnull' => false]);
        $table->addForeignKeyConstraint(self::MARELLO_CUSTOMER_TABLE, ['owner_marello_customer_id'], ['id']);
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

    /**
     * Sets the AttachmentExtension
     *
     * @param AttachmentExtension $attachmentExtension
     */
    public function setAttachmentExtension(AttachmentExtension $attachmentExtension)
    {
        $this->attachmentExtension = $attachmentExtension;
    }
}
