<?php

namespace Marello\Bundle\OrderBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Marello\Bundle\OrderBundle\Entity\Order;
use Marello\Bundle\OrderBundle\Entity\OrderItem;
use Marello\Component\Address\AddressInterface;
use Marello\Component\Order\OrderInterface;
use Marello\Component\Order\OrderItemInterface;
use Oro\Bundle\SecurityBundle\Annotation as Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Marello\Bundle\AddressBundle\Entity\Address;

class OrderController extends Controller
{
    /**
     * @Config\Route("/")
     * @Config\Template
     * @Security\AclAncestor("marello_order_view")
     */
    public function indexAction()
    {
        return ['entity_class' => 'MarelloOrderBundle:Order'];
    }

    /**
     * @Config\Route("/view/{id}", requirements={"id"="\d+"})
     * @Config\Template
     * @Security\AclAncestor("marello_order_view")
     *
     * @param OrderInterface $order
     *
     * @return array
     */
    public function viewAction(OrderInterface $order)
    {
        return ['entity' => $order];
    }

    /**
     * @Config\Route("/create")
     * @Config\Method({"GET", "POST"})
     * @Config\Template
     * @Security\AclAncestor("marello_order_create")
     *
     * @param Request $request
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        return $this->update($request);
    }

    /**
     * @Config\Route("/update/{id}", requirements={"id"="\d+"})
     * @Config\Method({"GET", "POST"})
     * @Config\Template
     * @Security\AclAncestor("marello_order_update")
     *
     * @param Request        $request
     * @param OrderInterface $order
     *
     * @return array
     */
    public function updateAction(Request $request, OrderInterface $order)
    {
        return $this->update($request, $order);
    }

    /**
     * Handles order updates and creation.
     *
     * @param Request        $request
     * @param OrderInterface $order
     *
     * @return array
     */
    protected function update(Request $request, OrderInterface $order = null)
    {
        $formName = $order ? 'marello_order_update' : 'marello_order_order';

        if ($order === null) {
            $order = new Order();
        }

        /*
         * Copy of original order items collection to be used as a reference to find detached order items.
         */
        $originalItems = new ArrayCollection($order->getItems()->toArray());

        $form = $this->createForm($formName, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('marello.order.messages.success.order.saved')
            );
            $manager = $this->getDoctrine()->getManager();

            /*
             * Remove detached order items.
             */
            $originalItems->filter(function (OrderItemInterface $originalItem) use ($order) {
                return false === $order->getItems()->contains($originalItem);
            })->map(function (OrderItemInterface $orderItem) use ($manager) {
                $manager->remove($orderItem);
            });

            $manager->persist($order);
            $manager->flush();

            return $this->get('oro_ui.router')->redirectAfterSave(
                [
                    'route'      => 'marello_order_order_update',
                    'parameters' => [
                        'id' => $order->getId(),
                    ],
                ],
                [
                    'route'      => 'marello_order_order_view',
                    'parameters' => [
                        'id' => $order->getId(),
                    ],
                ],
                $order
            );
        }

        return [
            'entity' => $order,
            'form'   => $form->createView(),
        ];
    }

    /**
     * @Config\Route("/widget/address/{id}/{typeId}", requirements={"id"="\d+","typeId"="\d+"})
     * @Config\Method({"GET", "POST"})
     * @Config\Template
     * @Security\AclAncestor("marello_order_update")
     *
     * @param Request          $request
     * @param AddressInterface $address
     *
     * @return array
     */
    public function addressAction(Request $request, AddressInterface $address)
    {
        return [
            'orderAddress' => $address,
            'addressType' => ((int)$request->get('typeId') === 1) ? 'billing' : 'shipping'
        ];
    }


    /**
     * @Config\Route("/update/address/{id}", requirements={"id"="\d+"})
     * @Config\Method({"GET", "POST"})
     * @Config\Template("MarelloOrderBundle:Order:widget/updateAddress.html.twig")
     * @Security\AclAncestor("marello_order_update")
     *
     * @param Request          $request
     * @param AddressInterface $address
     *
     * @return array
     */
    public function updateAddressAction(Request $request, AddressInterface $address)
    {
        $responseData = array(
            'saved' => false,
        );
        $form  = $this->createForm('marello_address', $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $responseData['orderAddress'] = $address;
            $responseData['saved'] = true;
        }

        $responseData['form'] = $form->createView();
        return $responseData;
    }
}
