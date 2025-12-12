<?php

namespace Marello\Bundle\InvoiceBundle\Migrations\Schema\v3_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;

class UpdateInvoiceRelatedTable implements Migration
{
    const INVOICE_TABLE_NAME = 'marello_invoice_invoice';
    const INVOICE_ITEM_TABLE_NAME = 'marello_invoice_invoice_item';

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $this->updateInvoiceTable($schema);
        $this->updateInvoiceItemTable($schema);
    }

    /**
     * {@inheritdoc}
     * @param Schema $schema
     * @param QueryBag $queries
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updateInvoiceTable(Schema $schema)
    {
        $table = $schema->getTable(self::INVOICE_TABLE_NAME);
        if (!$table->hasColumn('po_number')) {
            $table->addColumn('po_number', 'string', ['notnull' => false, 'length' => 255]);
        }

        if (!$table->hasColumn('discount_amount')) {
            $table->addColumn('discount_amount', 'money', ['notnull' => false, 'precision' => 19, 'scale' => 4, 'comment' => '(DC2Type:money)']);
        }
    }

    /**
     * {@inheritdoc}
     * @param Schema $schema
     * @param QueryBag $queries
     * @throws \Doctrine\DBAL\Schema\SchemaException
     */
    protected function updateInvoiceItemTable(Schema $schema)
    {
        $table = $schema->getTable(self::INVOICE_ITEM_TABLE_NAME);
        if (!$table->hasColumn('order_item_id')) {
            $table->addColumn('order_item_id', 'integer', ['notnull' => false]);
            $table->addForeignKeyConstraint(
                $schema->getTable('marello_order_order_item'),
                ['order_item_id'],
                ['id'],
                ['onDelete' => 'SET NULL', 'onUpdate' => null]
            );
        }
    }
}
