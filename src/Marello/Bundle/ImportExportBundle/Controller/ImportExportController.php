<?php

namespace Marello\Bundle\ImportExportBundle\Controller;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\ImportExportBundle\Controller\ImportExportController as BaseImportExportController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ImportExportController extends BaseImportExportController
{
    #[Route(path: '/import_validate_export', name: 'oro_importexport_import_validate_export_template_form')]
    #[AclAncestor('oro_importexport_import')]
    #[Template('@MarelloImportExport/ImportExport/widget/importValidateExportTemplate.html.twig')]
    public function importValidateExportTemplateFormAction(Request $request)
    {
        return parent::importValidateExportTemplateFormAction($request);
    }
}
