<?php

namespace Marello\Bundle\ReturnBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\ReturnBundle\Entity\ReturnEntity;
use Marello\Bundle\ReturnBundle\Form\Type\ReturnType;
use Marello\Bundle\ReturnBundle\Form\Type\ReturnUpdateType;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\UIBundle\Route\Router;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReturnController extends AbstractController
{
    #[Route(path: '/', name: 'marello_return_return_index')]
    #[Template]
    #[AclAncestor('marello_return_view')]
    public function indexAction()
    {
        return ['entity_class' => 'MarelloReturnBundle:ReturnEntity'];
    }

    /**
     *
     * @param Order   $order
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create/{id}', requirements: ['id' => '\d+'], name: 'marello_return_return_create')]
    #[Template]
    #[AclAncestor('marello_return_create')]
    public function createAction(Order $order, Request $request)
    {
        $return = new ReturnEntity();
        $return->setOrder($order);
        $return->setSalesChannel($order->getSalesChannel());
        $form = $this->createForm(ReturnType::class, $return);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->container->get(ManagerRegistry::class)->getManager();

            $manager->persist($return);
            $manager->flush();
            $request->getSession()->getFlashBag()->add(
                'success',
                $this->container
                    ->get(TranslatorInterface::class)
                    ->trans('marello.return.returnentity.messages.success.returnentity.saved')
            );

            return $this->container->get(Router::class)->redirect($return);
        }

        return [
            'form' => $form->createView()
        ];
    }

    /**
     * @param ReturnEntity $return
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_return_return_view')]
    #[Template]
    #[AclAncestor('marello_return_view')]
    public function viewAction(ReturnEntity $return)
    {
        return ['entity' => $return];
    }

    /**
     *
     * @param ReturnEntity $return
     * @param Request      $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route(path: '/update/{id}', requirements: ['id' => '\d+'], name: 'marello_return_return_update')]
    #[Template]
    #[AclAncestor('marello_return_update')]
    public function updateAction(ReturnEntity $return, Request $request)
    {
        $form = $this->createForm(ReturnUpdateType::class, $return);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->container->get(ManagerRegistry::class)->getManager();

            $manager->persist($return);
            $manager->flush();

            return $this->container->get(Router::class)->redirect($return);
        }

        return [
            'form' => $form->createView(),
        ];
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                ManagerRegistry::class,
                TranslatorInterface::class,
                Router::class,
            ]
        );
    }
}
