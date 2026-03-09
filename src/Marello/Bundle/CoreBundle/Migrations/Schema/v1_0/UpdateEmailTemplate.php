<?php

namespace Marello\Bundle\CoreBundle\Migrations\Data\ORM;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityConfigBundle\Entity\ConfigModel;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\ExtendOptionsManager;
use Oro\Bundle\EntityExtendBundle\Migration\OroOptions;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class UpdateEmailTemplate implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->updateEmailTemplateTable($schema);
        $this->updateTemplateNames($queries);
    }

    /**
     * Update oro_email_template table
     *
     * @param Schema $schema
     */
    protected function updateEmailTemplateTable(Schema $schema)
    {
        $table = $schema->getTable('oro_email_template');
        if (!$table->hasColumn('template_name')) {
            $table->addColumn(
                'template_name',
                'string',
                [
                    'notnull' => false,
                    OroOptions::KEY => [
                        'extend' => ['is_extend' => true, 'owner' => ExtendScope::OWNER_CUSTOM],
                        'form' => ['is_enabled' => true],
                        'entity' => ['label' => 'marello.core.emailtemplate.entity.template_name.label'],
                        'datagrid' => ['is_visible' => DatagridScope::IS_VISIBLE_FALSE],
                        'importexport' => ['excluded' => true],
                        ExtendOptionsManager::MODE_OPTION => ConfigModel::MODE_READONLY
                    ]
                ]
            );
        }
    }

    protected function updateTemplateNames(QueryBag $queries)
    {
        $sql = sprintf("UPDATE %s SET template_name = name;", 'oro_email_template');
        $queries->addPostQuery($sql);
        var_dump($sql);
    }
}
