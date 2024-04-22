<?php

namespace Marello\Bundle\CustomerBundle\Controller;

use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\CustomerBundle\Form\Type\CustomerType;
use Oro\Bundle\ActivityListBundle\Entity\Manager\ActivityListManager;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomerController extends AbstractController
{
    /**
     * @Template
     * @AclAncestor("marello_customer_view")
     * @return array
     */
    #[Route(path: '/', name: 'marello_customer_index')]
    public function indexAction()
    {
        return ['entity_class' => Customer::class];
    }

    /**
     * @Template
     * @AclAncestor("marello_customer_view")
     *
     * @param Customer $customer
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_customer_view')]
    public function viewAction(Customer $customer)
    {
        $entityClass = $this->container->get(EntityRoutingHelper::class)->resolveEntityClass('marellocustomers');
        $manager = $this->container->get(ActivityListManager::class);
        $results = $manager->getListData(
            $entityClass,
            1000,
            [],
            []
        );
        
        return ['entity' => $customer];
    }

    /**
     * @Template("@MarelloCustomer/Customer/update.html.twig")
     * @AclAncestor("marello_customer_create")
     * @return array
     */
    #[Route(path: '/create', methods: ['GET', 'POST'], name: 'marello_customer_create')]
    public function createAction(Request $request)
    {
        return $this->update($request);
    }

    /**
     * @Template
     * @AclAncestor("marello_customer_update")
     *
     * @param Customer $customer
     * @return array
     */
    #[Route(path: '/update/{id}', methods: ['GET', 'POST'], requirements: ['id' => '\d+'], name: 'marello_customer_update')]
    public function updateAction(Request $request, Customer $customer)
    {
        return $this->update($request, $customer);
    }

    private function update(Request $request, Customer $customer = null)
    {
        if (!$customer) {
            $customer = new Customer();
        }

        return $this->container->get(UpdateHandlerFacade::class)->update(
            $customer,
            $this->createForm(CustomerType::class, $customer),
            $this->container->get(TranslatorInterface::class)->trans('marello.order.messages.success.customer.saved'),
            $request,
            'marello_customer.form.handler.customer'
        );
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                EntityRoutingHelper::class,
                ActivityListManager::class,
                UpdateHandlerFacade::class,
                TranslatorInterface::class,
            ]
        );
    }
}
