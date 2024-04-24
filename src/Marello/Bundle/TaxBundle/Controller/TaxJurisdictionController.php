<?php

namespace Marello\Bundle\TaxBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use Marello\Bundle\TaxBundle\Entity\TaxJurisdiction;
use Marello\Bundle\TaxBundle\Form\Type\TaxJurisdictionType;

/**
 * Class TaxJurisdictionController
 * @package Marello\Bundle\TaxBundle\Controller
  */
class TaxJurisdictionController extends AbstractController
{
    /**
     * @return array
     */
    #[Route(path: '/', name: 'marello_tax_taxjurisdiction_index')]
    #[Template]
    #[AclAncestor('marello_tax_taxjurisdiction_view')]
    public function indexAction()
    {
        return [
            'entity_class' => TaxJurisdiction::class
        ];
    }

    /**
     *
     * @param TaxJurisdiction $taxJurisdiction
     * @return array
     */
    #[Route(path: '/view/{id}', name: 'marello_tax_taxjurisdiction_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'marello_tax_taxjurisdiction_view', type: 'entity', class: TaxJurisdiction::class, permission: 'VIEW')]
    public function viewAction(TaxJurisdiction $taxJurisdiction)
    {
        return [
            'entity' => $taxJurisdiction
        ];
    }

    /**
     *
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', name: 'marello_tax_taxjurisdiction_create')]
    #[Template('@MarelloTax/TaxJurisdiction/update.html.twig')]
    #[Acl(id: 'marello_tax_taxjurisdiction_create', type: 'entity', class: TaxJurisdiction::class, permission: 'CREATE')]
    public function createAction(Request $request)
    {
        return $this->update(new TaxJurisdiction(), $request);
    }

    /**
     *
     * @param Request $request
     * @param TaxJurisdiction $taxJurisdiction
     * @return array
     */
    #[Route(path: '/update/{id}', name: 'marello_tax_taxjurisdiction_update', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'marello_tax_taxjurisdiction_update', type: 'entity', class: TaxJurisdiction::class, permission: 'EDIT')]
    public function updateAction(Request $request, TaxJurisdiction $taxJurisdiction)
    {
        return $this->update($taxJurisdiction, $request);
    }

    /**
     * @param TaxJurisdiction $taxJurisdiction
     * @param Request $request
     * @return array|RedirectResponse
     */
    protected function update(TaxJurisdiction $taxJurisdiction, Request $request)
    {
        return $this->container->get(UpdateHandlerFacade::class)->update(
            $taxJurisdiction,
            $this->createForm(TaxJurisdictionType::class, $taxJurisdiction),
            $this->container->get(TranslatorInterface::class)->trans('marello.tax.messages.success.taxjurisdiction.saved'),
            $request
        );
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                UpdateHandlerFacade::class,
                TranslatorInterface::class,
            ]
        );
    }
}
