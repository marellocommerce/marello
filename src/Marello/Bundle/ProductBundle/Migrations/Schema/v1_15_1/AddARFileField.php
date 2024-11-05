<?php

namespace Marello\Bundle\ProductBundle\Migrations\Schema\v1_15_1;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareTrait;
use Oro\Bundle\AttachmentBundle\Migration\Extension\AttachmentExtensionAwareInterface;

use Marello\Bundle\ProductBundle\Migrations\Schema\MarelloProductBundleInstaller;

class AddARFileField implements Migration, AttachmentExtensionAwareInterface
{
    use AttachmentExtensionAwareTrait;

    public function up(Schema $schema, QueryBag $queries)
    {
        // add ARFile relation
        $this->attachmentExtension->addFileRelation(
            $schema,
            MarelloProductBundleInstaller::PRODUCT_TABLE,
            'ARFile',
            [
                'importexport' => ['excluded' => true],
                'attribute' => ['is_attribute' => true],
                'extend' => ['owner' => ExtendScope::OWNER_CUSTOM],
                'attachment' => ['mimetypes' => 'application/zip,model/vnd.usdz+zip', 'acl_protected' => false]
            ],
            MarelloProductBundleInstaller::MAX_PRODUCT_ARFILE_SIZE_IN_MB
        );
    }
}
