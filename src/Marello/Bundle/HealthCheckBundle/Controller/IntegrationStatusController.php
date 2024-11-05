<?php

namespace Marello\Bundle\HealthCheckBundle\Controller;

use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IntegrationStatusController extends AbstractController
{
    #[Route(path: '/status', name: 'marello_healthcheck_integration_statuses_index')]
    #[AclAncestor('oro_integration_view')]
    #[Template]
    public function indexAction()
    {
        return [];
    }
}
