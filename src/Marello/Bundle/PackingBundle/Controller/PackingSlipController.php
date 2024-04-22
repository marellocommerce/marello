<?php

namespace Marello\Bundle\PackingBundle\Controller;

use Marello\Bundle\PackingBundle\Entity\PackingSlip;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PackingSlipController extends AbstractController
{
    /**
     * @Template("@MarelloPacking/PackingSlip/index.html.twig")
     * @AclAncestor("marello_packing_slip_view")
     */
    #[Route(path: '/', name: 'marello_packing_packingslip_index')]
    public function indexAction()
    {
        return ['entity_class' => 'MarelloPackingBundle:PackingSlip'];
    }

    /**
     * @Template("@MarelloPacking/PackingSlip/view.html.twig")
     * @AclAncestor("marello_packing_slip_view")
     *
     * @param PackingSlip $packingSlip
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_packing_packingslip_view')]
    public function viewAction(PackingSlip $packingSlip)
    {
        return ['entity' => $packingSlip];
    }
}
