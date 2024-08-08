<?php

namespace Marello\Bundle\ProductBundle\Migrations\Schema\v1_15_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\EntityConfigBundle\Migration\UpdateEntityConfigFieldValueQuery;

use Marello\Bundle\ProductBundle\Entity\Product;

class AddARFileField implements Migration
{
    public function up(Schema $schema, QueryBag $queries)
    {
        $table = $schema->getTable('marello_product_product');
        $table->addColumn('ar_file_id', 'integer', ['notnull' => false]);
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_attachment_file'),
            ['ar_file_id'],
            ['id']
        );

        $queries->addPostQuery(
            new UpdateEntityConfigFieldValueQuery(Product::class, 'ARFile', 'attribute', 'is_attribute', true)
        );
        $queries->addPostQuery(
            new UpdateEntityConfigFieldValueQuery(Product::class, 'ARFile', 'importexport', 'excluded', true)
        );
        $queries->addPostQuery(
            new UpdateEntityConfigFieldValueQuery(Product::class, 'ARFile', 'extend', 'owner', ExtendScope::OWNER_CUSTOM)
        );
        $queries->addPostQuery(
            new UpdateEntityConfigFieldValueQuery(Product::class, 'ARFile', 'attachment', 'maxsize', 50)
        );
        $queries->addPostQuery(
            new UpdateEntityConfigFieldValueQuery(Product::class, 'ARFile', 'attachment', 'mimetypes', 'application/zip,model/vnd.usdz+zip')
        );
    }
}
