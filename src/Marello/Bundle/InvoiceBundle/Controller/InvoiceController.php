<?php

namespace Marello\Bundle\InvoiceBundle\Controller;

use Marello\Bundle\InvoiceBundle\Entity\AbstractInvoice;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{
    /**
     * @Template
     * @AclAncestor("marello_invoice_view")
     */
    #[Route(path: '/', name: 'marello_invoice_invoice_index')]
    public function indexAction()
    {
        return ['entity_class' => AbstractInvoice::class];
    }

    /**
     * @Template
     * @AclAncestor("marello_invoice_view")
     *
     * @param AbstractInvoice $invoice
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_invoice_invoice_view')]
    public function viewAction(AbstractInvoice $invoice)
    {
        return ['entity' => $invoice];
    }
}
