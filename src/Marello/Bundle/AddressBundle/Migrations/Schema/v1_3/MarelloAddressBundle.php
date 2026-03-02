<?php

namespace Marello\Bundle\AddressBundle\Migrations\Schema\v1_3;

use Doctrine\DBAL\Schema\Schema;

use Marello\Bundle\AddressBundle\Entity\MarelloTypedAddress;
use Oro\Bundle\EntityConfigBundle\Migration\RemoveFieldQuery;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 */
class MarelloAddressBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->updateMarelloTypedAddressTable($schema, $queries);
    }

    /**
     * Update marello_address table
     *
     * @param Schema $schema
     * @param QueryBag $queries
     */
    protected function updateMarelloTypedAddressTable(Schema $schema, QueryBag $queries)
    {
        if ($schema->hasTable('marello_typed_address')) {
            $table = $schema->getTable('marello_typed_address');
            if ($table->hasColumn('organization')) {
                $table->dropColumn('organization');
            }

            $dropFieldsQuery = new RemoveFieldQuery(MarelloTypedAddress::class, 'organization');
            $queries->addPostQuery($dropFieldsQuery);
        }
    }
}
