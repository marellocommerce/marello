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

use Marello\Bundle\TaxBundle\Entity\TaxRate;
use Marello\Bundle\TaxBundle\Form\Handler\TaxRateHandler;

/**
 * Class TaxRateController
 * @package Marello\Bundle\TaxBundle\Controller
 */
class TaxRateController extends AbstractController
{
    #[Route(path: '/', name: 'marello_tax_taxrate_index')]
    #[Template]
    #[AclAncestor('marello_tax_taxrate_view')]
    public function indexAction()
    {
        return ['entity_class' => TaxRate::class];
    }

    /**
     *
     * @param TaxRate $taxRate
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_tax_taxrate_view')]
    #[Template]
    #[Acl(id: 'marello_tax_taxrate_view', type: 'entity', class: TaxRate::class, permission: 'VIEW')]
    public function viewAction(TaxRate $taxRate)
    {
        return ['entity' => $taxRate];
    }

    /**
     *
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', methods: ['GET', 'POST'], name: 'marello_tax_taxrate_create')]
    #[Template]
    #[Acl(id: 'marello_tax_taxrate_create', type: 'entity', class: TaxRate::class, permission: 'CREATE')]
    public function createAction(Request $request)
    {
        return $this->update($request, new TaxRate());
    }

    /**
     *
     * @param TaxRate $taxRate
     * @param Request $request
     * @return array
     */
    #[Route(path: '/update/{id}', methods: ['GET', 'POST'], requirements: ['id' => '\d+'], name: 'marello_tax_taxrate_update')]
    #[Template]
    #[Acl(id: 'marello_tax_taxrate_update', type: 'entity', class: TaxRate::class, permission: 'EDIT')]
    public function updateAction(TaxRate $taxRate, Request $request)
    {
        return $this->update($request, $taxRate);
    }

    /**
     * Handles supplier updates and creation.
     *
     * @param Request $request
     * @param TaxRate $taxRate
     * @return array
     */
    protected function update(Request $request, TaxRate $taxRate = null)
    {
        $handler = $this->container->get(TaxRateHandler::class);
        
        if ($handler->process($taxRate)) {
            $request->getSession()->getFlashBag()->add(
                'success',
                $this->container->get(TranslatorInterface::class)->trans('marello.tax.messages.success.taxrate.saved')
            );
            
            return $this->container->get(Router::class)->redirect($taxRate);
        }

        return [
            'entity' => $taxRate,
            'form'   => $handler->getFormView(),
        ];
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TaxRateHandler::class,
                TranslatorInterface::class,
                Router::class,
            ]
        );
    }
}
