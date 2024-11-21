<?php

namespace Marello\Bundle\PaymentBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\UIBundle\Route\Router;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use Marello\Bundle\PaymentBundle\Entity\Payment;
use Marello\Bundle\PaymentBundle\Form\Handler\PaymentCreateHandler;
use Marello\Bundle\PaymentBundle\Form\Handler\PaymentUpdateHandler;

class PaymentController extends AbstractController
{
    /**
     * @return array
     */
    #[Route(path: '/', name: 'marello_payment_index')]
    #[Template('@MarelloPayment/Payment/index.html.twig')]
    #[AclAncestor('marello_payment_view')]
    public function indexAction()
    {
        return [
            'entity_class' => Payment::class
        ];
    }

    /**
     *
     * @param Payment $payment
     * @return array
     */
    #[Route(path: '/view/{id}', name: 'marello_payment_view', requirements: ['id' => '\d+'])]
    #[Template('@MarelloPayment/Payment/view.html.twig')]
    #[Acl(id: 'marello_payment_view', type: 'entity', class: Payment::class, permission: 'VIEW')]
    public function viewAction(Payment $payment)
    {
        return [
            'entity' => $payment,
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', name: 'marello_payment_create')]
    #[Template('@MarelloPayment/Payment/create.html.twig')]
    #[Acl(id: 'marello_payment_create', type: 'entity', permission: 'CREATE', class: Payment::class)]
    public function createAction(Request $request)
    {
        return $this->update($request);
    }

    /**
     * @param Request $request
     * @param Payment $entity
     * @return array
     */
    #[Route(path: '/update/{id}', name: 'marello_payment_update', requirements: ['id' => '\d+'])]
    #[Template('@MarelloPayment/Payment/update.html.twig')]
    #[Acl(id: 'marello_payment_update', type: 'entity', permission: 'EDIT', class: Payment::class)]
    public function updateAction(Request $request, Payment $entity)
    {
        return $this->update($request, $entity);
    }

    /**
     * @param Request $request
     * @param Payment|null $entity
     * @return array|RedirectResponse
     */
    protected function update(Request $request, Payment $entity = null)
    {
        if ($entity === null) {
            $entity = new Payment();
            $handler = $this->container->get(PaymentCreateHandler::class);
        } else {
            $handler = $this->container->get(PaymentUpdateHandler::class);
        }

        if ($handler->process($entity)) {
            $request->getSession()->getFlashBag()->add(
                'success',
                $this->container->get(TranslatorInterface::class)->trans('marello.payment_term.ui.payment_term.saved.message')
            );

            return $this->container->get(Router::class)->redirect($entity);
        }

        return [
            'entity' => $entity,
            'form'   => $handler->getFormView(),
        ];
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                PaymentCreateHandler::class,
                PaymentUpdateHandler::class,
                TranslatorInterface::class,
                Router::class,
            ]
        );
    }
}
