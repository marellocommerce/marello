<?php

namespace Marello\Bundle\TaxBundle\Controller;

use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Marello\Bundle\TaxBundle\Form\Handler\TaxCodeHandler;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\UIBundle\Route\Router;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TaxCodeController
 * @package Marello\Bundle\TaxBundle\Controller
 */
class TaxCodeController extends AbstractController
{
    /**
     * @Template
     * @AclAncestor("marello_tax_taxcode_view")
     */
    #[Route(path: '/', name: 'marello_tax_taxcode_index')]
    public function indexAction()
    {
        return ['entity_class' => 'MarelloTaxBundle:TaxCode'];
    }

    /**
     * @Template
     * @Acl(
     *      id="marello_tax_taxcode_view",
     *      type="entity",
     *      class="MarelloTaxBundle:TaxCode",
     *      permission="VIEW"
     * )
     *
     * @param TaxCode $taxCode
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_tax_taxcode_view')]
    public function viewAction(TaxCode $taxCode)
    {
        return ['entity' => $taxCode];
    }

    /**
     * @Template
     * @Acl(
     *      id="marello_tax_taxcode_create",
     *      type="entity",
     *      class="MarelloTaxBundle:TaxCode",
     *      permission="CREATE"
     * )
     *
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', methods: ['GET', 'POST'], name: 'marello_tax_taxcode_create')]
    public function createAction(Request $request)
    {
        return $this->update($request, new TaxCode());
    }

    /**
     * @Template
     * @Acl(
     *      id="marello_tax_taxcode_update",
     *      type="entity",
     *      class="MarelloTaxBundle:TaxCode",
     *      permission="EDIT"
     * )
     *
     * @param TaxCode $taxCode
     * @param Request $request
     * @return array
     */
    #[Route(path: '/update/{id}', methods: ['GET', 'POST'], requirements: ['id' => '\d+'], name: 'marello_tax_taxcode_update')]
    public function updateAction(TaxCode $taxCode, Request $request)
    {
        return $this->update($request, $taxCode);
    }

    /**
     * Handles supplier updates and creation.
     *
     * @param Request $request
     * @param TaxCode $taxCode
     * @return array
     */
    protected function update(Request $request, TaxCode $taxCode = null)
    {
        $handler = $this->container->get(TaxCodeHandler::class);
        
        if ($handler->process($taxCode)) {
            $request->getSession()->getFlashBag()->add(
                'success',
                $this->container->get(TranslatorInterface::class)->trans('marello.tax.messages.success.taxcode.saved')
            );
            
            return $this->container->get(Router::class)->redirect($taxCode);
        }

        return [
            'entity' => $taxCode,
            'form'   => $handler->getFormView(),
        ];
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TaxCodeHandler::class,
                TranslatorInterface::class,
                Router::class,
            ]
        );
    }
}
