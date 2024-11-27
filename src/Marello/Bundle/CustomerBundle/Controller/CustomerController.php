<?php

namespace Marello\Bundle\CustomerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\EntityBundle\Tools\EntityRoutingHelper;
use Oro\Bundle\ActivityListBundle\Entity\Manager\ActivityListManager;

use Marello\Bundle\CustomerBundle\Entity\Customer;
use Marello\Bundle\CustomerBundle\Form\Type\CustomerType;

class CustomerController extends AbstractController
{
    /**
     * @return array
     */
    #[Route(path: '/', name: 'marello_customer_index')]
    #[Template]
    #[AclAncestor('marello_customer_view')]
    public function indexAction()
    {
        return ['entity_class' => Customer::class];
    }

    /**
     * @param Customer $customer
     * @return array
     */
    #[Route(path: '/view/{id}', name: 'marello_customer_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[AclAncestor('marello_customer_view')]
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
     * @return array
     */
    #[Route(path: '/create', name: 'marello_customer_create', methods: ['GET', 'POST'])]
    #[Template('@MarelloCustomer/Customer/update.html.twig')]
    #[AclAncestor('marello_customer_create')]
    public function createAction(Request $request)
    {
        return $this->update($request);
    }

    /**
     * @param Customer $customer
     * @return array
     */
    #[Route(path: '/update/{id}', name: 'marello_customer_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template]
    #[AclAncestor('marello_customer_update')]
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
