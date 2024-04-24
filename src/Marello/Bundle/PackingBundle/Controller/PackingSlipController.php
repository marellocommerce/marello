<?php

namespace Marello\Bundle\PackingBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use Marello\Bundle\PackingBundle\Entity\PackingSlip;

class PackingSlipController extends AbstractController
{
    #[Route(path: '/', name: 'marello_packing_packingslip_index')]
    #[Template('@MarelloPacking/PackingSlip/index.html.twig')]
    #[AclAncestor('marello_packing_slip_view')]
    public function indexAction()
    {
        return ['entity_class' => PackingSlip::class];
    }

    /**
     * @param PackingSlip $packingSlip
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_packing_packingslip_view')]
    #[Template('@MarelloPacking/PackingSlip/view.html.twig')]
    #[AclAncestor('marello_packing_slip_view')]
    public function viewAction(PackingSlip $packingSlip)
    {
        return ['entity' => $packingSlip];
    }
}
