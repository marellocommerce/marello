<?php

namespace Marello\Bundle\CustomerBundle\Controller;

use Marello\Bundle\CustomerBundle\Entity\Company;
use Marello\Bundle\CustomerBundle\Form\Handler\CompanyHandler;
use Marello\Bundle\CustomerBundle\JsTree\CompanyTreeHandler;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\UIBundle\Route\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

class CompanyController extends AbstractController
{
    /**
     * @Template
     * @AclAncestor("marello_customer_company_view")
     * @return array
     */
    #[Route(path: '/', name: 'marello_customer_company_index')]
    public function indexAction()
    {
        return [
            'entity_class' => Company::class
        ];
    }

    /**
     * @Template
     * @Acl(
     *      id="marello_customer_company_view",
     *      type="entity",
     *      class="MarelloCustomerBundle:Company",
     *      permission="VIEW"
     * )
     *
     * @param Company $company
     * @return array
     */
    #[Route(path: '/view/{id}', name: 'marello_customer_company_view', requirements: ['id' => '\d+'])]
    public function viewAction(Company $company)
    {
        return [
            'entity' => $company,
            'treeData' => $this->container->get(CompanyTreeHandler::class)->createTree($company),
        ];
    }

    /**
     * @Template("@MarelloCustomer/Company/update.html.twig")
     * @Acl(
     *      id="marello_customer_company_create",
     *      type="entity",
     *      class="MarelloCustomerBundle:Company",
     *      permission="CREATE"
     * )
     *
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', name: 'marello_customer_company_create')]
    public function createAction(Request $request)
    {
        return $this->update(new Company(), $request);
    }

    /**
     * @Template
     * @Acl(
     *      id="marello_customer_company_update",
     *      type="entity",
     *      class="MarelloCustomerBundle:Company",
     *      permission="EDIT"
     * )
     *
     * @param Company $company
     * @param Request $request
     * @return array
     */
    #[Route(path: '/update/{id}', name: 'marello_customer_company_update', requirements: ['id' => '\d+'])]
    public function updateAction(Company $company, Request $request)
    {
        return $this->update($company, $request);
    }

    /**
     * @param Company $company
     * @param Request $request
     * @return array|RedirectResponse
     */
    protected function update(Company $company, Request $request)
    {
        $handler = $this->container->get(CompanyHandler::class);

        if ($handler->process($company)) {
            $request->getSession()->getFlashBag()->add(
                'success',
                $this->container->get(TranslatorInterface::class)->trans('marello.customer.controller.company.saved.message')
            );

            return $this->container->get(Router::class)->redirect($company);
        }

        return [
            'entity' => $company,
            'form'   => $handler->getFormView(),
        ];
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                CompanyTreeHandler::class,
                CompanyHandler::class,
                TranslatorInterface::class,
                Router::class,
            ]
        );
    }
}
