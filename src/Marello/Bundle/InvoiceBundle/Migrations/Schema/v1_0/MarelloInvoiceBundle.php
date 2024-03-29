<?php

namespace Marello\Bundle\InvoiceBundle\Migrations\Schema\v1_0;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class MarelloInvoiceBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createMarelloInvoiceInvoiceTable($schema);
        $this->createMarelloInvoiceInvoiceItemTable($schema);

        /** Foreign keys generation **/
        $this->addMarelloInvoiceInvoiceForeignKeys($schema);
        $this->addMarelloInvoiceInvoiceItemForeignKeys($schema);
    }

    /**
     * Create marello_invoice_invoice table
     *
     * @param Schema $schema
     */
    protected function createMarelloInvoiceInvoiceTable(Schema $schema)
    {
        $table = $schema->createTable('marello_invoice_invoice');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('invoice_number', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('billing_address_id', 'integer', ['notnull' => false]);
        $table->addColumn('shipping_address_id', 'integer', ['notnull' => false]);
        $table->addColumn('invoiced_at', 'datetime', ['notnull' => false]);
        $table->addColumn('invoice_due_date', 'datetime', ['notnull' => false]);
        $table->addColumn('payment_method', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('payment_reference', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('payment_details', 'text', ['notnull' => false]);
        $table->addColumn('shipping_method', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('shipping_method_type', 'string', ['notnull' => false, 'length' => 255]);
        $table->addColumn('order_id', 'integer', ['notnull' => true]);
        $table->addColumn('currency', 'string', ['notnull' => false, 'length' => 10]);
        $table->addColumn('type', 'string', ['notnull' => true]);
        $table->addColumn('invoice_type', 'string', ['notnull' => false]);
        $table->addColumn('status', 'string', ['notnull' => false, 'length' => 10]);
        $table->addColumn('customer_id', 'integer', ['notnull' => false]);
        $table->addColumn('salesChannel_id', 'integer', ['notnull' => false]);
        $table->addColumn('subtotal', 'money', ['precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
        $table->addColumn('total_tax', 'money', ['precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
        $table->addColumn('grand_total', 'money', ['precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
        $table->addColumn(
            'shipping_amount_incl_tax',
            'money',
            [
                'notnull' => false, 'precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)'
            ]
        );
        $table->addColumn(
            'shipping_amount_excl_tax',
            'money',
            [
                'notnull' => false, 'precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)'
            ]
        );
        $table->addColumn('created_at', 'datetime');
        $table->addColumn('updated_at', 'datetime', ['notnull' => false]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['invoice_number'], 'UNIQ_45AB65072DA68207');
        $table->addIndex(['order_id'], 'IDX_A619DD647BE036FC1');
        $table->addIndex(['customer_id'], 'IDX_A619DD649395C3F31', []);
        $table->addIndex(['billing_address_id'], 'IDX_A619DD6443656FE61', []);
        $table->addIndex(['shipping_address_id'], 'IDX_A619DD64B1835C8F1', []);
        $table->addIndex(['salesChannel_id'], 'IDX_A619DD644C7A5B2E1', []);
        $table->addIndex(['organization_id']);
    }

    /**
     * Create marello_invoice_invoice_item table
     *
     * @param Schema $schema
     */
    protected function createMarelloInvoiceInvoiceItemTable(Schema $schema)
    {
        $table = $schema->createTable('marello_invoice_invoice_item');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('invoice_item_type', 'string', []);
        $table->addColumn('invoice_id', 'integer', ['notnull' => false]);
        $table->addColumn('product_id', 'integer', ['notnull' => false]);
        $table->addColumn('product_name', 'string', ['length' => 255]);
        $table->addColumn('product_sku', 'string', ['length' => 255]);
        $table->addColumn('quantity', 'integer', []);
        $table->addColumn('price', 'money', ['precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
        $table->addColumn('tax', 'money', ['precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
        $table->addColumn(
            'discount_amount',
            'money',
            [
                'notnull' => false, 'precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)'
            ]
        );
        $table->addColumn(
            'row_total_incl_tax',
            'money',
            [
                'precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)'
            ]
        );
        $table->addColumn(
            'row_total_excl_tax',
            'money',
            [
                'precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)'
            ]
        );
        $table->setPrimaryKey(['id']);
    }

    /**
     * Add marello_invoice_invoice foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloInvoiceInvoiceForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_invoice_invoice');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_order_order'),
            ['order_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_address'),
            ['billing_address_id'],
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
            $schema->getTable('marello_sales_sales_channel'),
            ['salesChannel_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_order_customer'),
            ['customer_id'],
            ['id'],
            ['onDelete' => null, 'onUpdate' => null]
        );
    }

    /**
     * Add marello_invoice_invoice_item foreign keys.
     *
     * @param Schema $schema
     */
    protected function addMarelloInvoiceInvoiceItemForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('marello_invoice_invoice_item');
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_product_product'),
            ['product_id'],
            ['id'],
            ['onDelete' => 'SET NULL', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('marello_invoice_invoice'),
            ['invoice_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}
