<?php

namespace Marello\Bundle\TaxBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\UIBundle\Route\Router;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use Marello\Bundle\TaxBundle\Entity\TaxCode;
use Marello\Bundle\TaxBundle\Form\Handler\TaxCodeHandler;

/**
 * Class TaxCodeController
 * @package Marello\Bundle\TaxBundle\Controller
 */
class TaxCodeController extends AbstractController
{
    #[Route(path: '/', name: 'marello_tax_taxcode_index')]
    #[Template]
    #[AclAncestor('marello_tax_taxcode_view')]
    public function indexAction()
    {
        return ['entity_class' => TaxCode::class];
    }

    /**
     *
     * @param TaxCode $taxCode
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_tax_taxcode_view')]
    #[Template]
    #[Acl(id: 'marello_tax_taxcode_view', type: 'entity', class: TaxCode::class, permission: 'VIEW')]
    public function viewAction(TaxCode $taxCode)
    {
        return ['entity' => $taxCode];
    }

    /**
     *
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', methods: ['GET', 'POST'], name: 'marello_tax_taxcode_create')]
    #[Template]
    #[Acl(id: 'marello_tax_taxcode_create', type: 'entity', class: TaxCode::class, permission: 'CREATE')]
    public function createAction(Request $request)
    {
        return $this->update($request, new TaxCode());
    }

    /**
     *
     * @param TaxCode $taxCode
     * @param Request $request
     * @return array
     */
    #[Route(path: '/update/{id}', methods: ['GET', 'POST'], requirements: ['id' => '\d+'], name: 'marello_tax_taxcode_update')]
    #[Template]
    #[Acl(id: 'marello_tax_taxcode_update', type: 'entity', class: TaxCode::class, permission: 'EDIT')]
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
