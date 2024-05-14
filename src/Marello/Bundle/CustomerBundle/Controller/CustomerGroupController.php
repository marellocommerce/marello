<?php

namespace Marello\Bundle\CustomerBundle\Controller;

use Marello\Bundle\CustomerBundle\Entity\CustomerGroup;
use Marello\Bundle\CustomerBundle\Form\Handler\CustomerGroupHandler;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\UIBundle\Route\Router;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerGroupController extends AbstractController
{
    /**
     * @Route(
     *     path="/",
     *     name="marello_customer_group_index"
     * )
     * @Template
     * @AclAncestor("marello_customer_group_view")
     *
     * @return array
     */
    public function indexAction()
    {
        return [
            'entity_class' => CustomerGroup::class
        ];
    }

    /**
     * @Route(
     *     path="/view/{id}",
     *     name="marello_customer_group_view",
     *     requirements={"id"="\d+"}
     * )
     * @Template
     */
    public function viewAction(CustomerGroup $customerGroup)
    {
        return [
            'entity' => $customerGroup
        ];
    }

    /**
     * @Route(
     *     path="/create",
     *     name="marello_customer_group_create"
     * )
     * @Template("@MarelloCustomer/CustomerGroup/update.html.twig")
     *
     * @param Request $request
     * @return array
     */
    public function createAction(Request $request)
    {
        return $this->update(new CustomerGroup(), $request);
    }

    /**
     * @Route(
     *     path="/update/{id}",
     *     name="marello_customer_group_update",
     *     requirements={"id"="\d+"}
     * )
     * @Template
     *
     * @param CustomerGroup $customerGroup
     * @param Request $request
     * @return array
     */
    public function updateAction(CustomerGroup $customerGroup, Request $request)
    {
        return $this->update($customerGroup, $request);
    }

    /**
     * @param CustomerGroup $customerGroup
     * @param Request $request
     * @return array|RedirectResponse
     */
    protected function update(CustomerGroup $customerGroup, Request $request)
    {
        $handler = $this->container->get(CustomerGroupHandler::class);

        if ($handler->process($customerGroup)) {
            $request->getSession()->getFlashBag()->add(
                'success',
                $this->container->get(TranslatorInterface::class)->trans('marello.customer_group.saved.message')
            );

            return $this->container->get(Router::class)->redirect($customerGroup);
        }

        return [
            'entity' => $customerGroup,
            'form'   => $handler->getFormView(),
        ];
    }

    public static function getSubscribedServices()
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                CustomerGroupHandler::class,
                TranslatorInterface::class,
                Router::class,
            ]
        );
    }
}