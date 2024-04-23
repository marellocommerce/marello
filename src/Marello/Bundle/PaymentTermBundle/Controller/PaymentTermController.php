<?php

namespace Marello\Bundle\PaymentTermBundle\Controller;

use Marello\Bundle\PaymentTermBundle\Entity\PaymentTerm;
use Marello\Bundle\PaymentTermBundle\Form\Handler\PaymentTermFormHandler;
use Oro\Bundle\UIBundle\Route\Router;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

class PaymentTermController extends AbstractController
{
    /**
     * @return array
     */
    #[Route(path: '/', name: 'marello_paymentterm_paymentterm_index')]
    #[AclAncestor('marello_paymentterm_paymentterm_view')]
    #[Template('@MarelloPaymentTerm/PaymentTerm/index.html.twig')]
    public function indexAction()
    {
        return [
            'entityClass' => PaymentTerm::class,
        ];
    }

    /**
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route(path: '/create', name: 'marello_paymentterm_paymentterm_create')]
    #[AclAncestor('marello_paymentterm_paymentterm_create')]
    #[Template('@MarelloPaymentTerm/PaymentTerm/update.html.twig')]
    public function createAction(Request $request)
    {
        $entity = new PaymentTerm();

        return $this->update($entity, $request);
    }

    /**
     *
     * @param PaymentTerm $entity
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route(path: '/update/{id}', requirements: ['id' => '\d+'], name: 'marello_paymentterm_paymentterm_update')]
    #[AclAncestor('marello_paymentterm_paymentterm_update')]
    #[Template('@MarelloPaymentTerm/PaymentTerm/update.html.twig')]
    public function updateAction(PaymentTerm $entity, Request $request)
    {
        return $this->update($entity, $request);
    }

    /**
     * @param PaymentTerm $entity
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function update(PaymentTerm $entity, Request $request)
    {
        $handler = $this->container->get(PaymentTermFormHandler::class);

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

    /**
     * @param PaymentTerm $entity
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_paymentterm_paymentterm_view')]
    #[AclAncestor('marello_paymentterm_paymentterm_view')]
    #[Template('@MarelloPaymentTerm/PaymentTerm/view.html.twig')]
    public function viewAction(PaymentTerm $entity)
    {
        return [
            'entity' => $entity,
        ];
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                PaymentTermFormHandler::class,
                TranslatorInterface::class,
                Router::class,
            ]
        );
    }
}
