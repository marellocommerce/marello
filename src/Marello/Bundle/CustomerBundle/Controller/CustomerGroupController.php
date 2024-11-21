<?php

namespace Marello\Bundle\CustomerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\UIBundle\Route\Router;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use Marello\Bundle\CustomerBundle\Entity\CustomerGroup;
use Marello\Bundle\CustomerBundle\Form\Handler\CustomerGroupHandler;

class CustomerGroupController extends AbstractController
{
    /**
     * @return array
     */
    #[Route(path: '/', name: 'marello_customer_group_index')]
    #[Template]
    #[AclAncestor('marello_customer_group_view')]
    public function indexAction()
    {
        return [
            'entity_class' => CustomerGroup::class
        ];
    }

    /**
     * @param CustomerGroup $customerGroup
     * @return CustomerGroup[]
     */
    #[Route(path: '/view/{id}', name: 'marello_customer_group_view', requirements: ['id' => '\d+'])]
    #[Template]
    public function viewAction(CustomerGroup $customerGroup)
    {
        return [
            'entity' => $customerGroup
        ];
    }

    /**
     * @param Request $request
     * @return array|RedirectResponse
     */
    #[Route(path: '/create', name: 'marello_customer_group_create', methods: ['GET', 'POST'])]
    #[Template('@MarelloCustomer/CustomerGroup/update.html.twig')]
    #[AclAncestor('marello_customer_group_create')]
    public function createAction(Request $request)
    {
        return $this->update(new CustomerGroup(), $request);
    }

    /**
     * @param CustomerGroup $customerGroup
     * @param Request $request
     * @return array|RedirectResponse
     */
    #[Route(
        path: '/update/{id}',
        name: 'marello_customer_group_update',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST']
    )]
    #[Template]
    #[AclAncestor('marello_customer_group_update')]
    public function updateAction(CustomerGroup $customerGroup, Request $request)
    {
        return $this->update($customerGroup, $request);
    }

    /**
     * @param CustomerGroup $customerGroup
     * @param Request $request
     * @return array|RedirectResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function update(CustomerGroup $customerGroup, Request $request)
    {
        $handler = $this->container->get(CustomerGroupHandler::class);

        if ($handler->process($customerGroup)) {
            $request->getSession()->getFlashBag()->add(
                'success',
                $this->container
                    ->get(TranslatorInterface::class)
                    ->trans('marello.customer.customergroup.saved.message')
            );

            return $this->container->get(Router::class)->redirect($customerGroup);
        }

        return [
            'entity' => $customerGroup,
            'form'   => $handler->getFormView(),
        ];
    }

    public static function getSubscribedServices(): array
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
