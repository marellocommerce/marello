<?php

namespace Marello\Bundle\ReportBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\DataGridBundle\Datagrid\Manager;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

class ReportController extends AbstractController
{
    /**
     *
     * @param string $reportGroupName
     * @param string $reportName
     * @return array
     */
    #[Route(
        path: '/static/{reportGroupName}/{reportName}/{_format}',
        name: 'marello_report_index',
        requirements: [
            'reportGroupName' => '\w+',
            'reportName' => '\w+',
            '_format' => 'html|json'],
        defaults: ['_format' => 'html']
    )]
    #[Template]
    #[AclAncestor('oro_report_view')]
    public function indexAction($reportGroupName, $reportName)
    {
        $gridName  = implode('-', ['marello_report', $reportGroupName, $reportName]);
        $pageTitle = $this->container->get(Manager::class)->getConfigurationForGrid($gridName)['pageTitle'];

        return [
            'pageTitle' => $this->container->get(TranslatorInterface::class)->trans($pageTitle),
            'gridName'  => $gridName,
            'params'    => [
                'reportGroupName' => $reportGroupName,
                'reportName'      => $reportName
            ]
        ];
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                Manager::class,
                TranslatorInterface::class,
            ]
        );
    }
}
