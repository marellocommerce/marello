<?php

namespace Marello\Bundle\HealthCheckBundle\Controller;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IntegrationStatusController extends AbstractController
{
    /**
     * @AclAncestor("oro_integration_view")
     * @Template()
     */
    #[Route(path: '/status', name: 'marello_healthcheck_integration_statuses_index')]
    public function indexAction()
    {
        return [];
    }
}
