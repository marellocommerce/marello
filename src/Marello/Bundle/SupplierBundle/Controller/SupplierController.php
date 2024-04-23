<?php

namespace Marello\Bundle\SupplierBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;

use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;

use Marello\Bundle\SupplierBundle\Entity\Supplier;
use Marello\Bundle\AddressBundle\Form\Type\AddressType;
use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Marello\Bundle\SupplierBundle\Form\Type\SupplierType;
use Marello\Bundle\SupplierBundle\Provider\SupplierProvider;

class SupplierController extends AbstractController
{
    #[Route(path: '/', name: 'marello_supplier_supplier_index')]
    #[Template]
    #[AclAncestor('marello_supplier_view')]
    public function indexAction()
    {
        return ['entity_class' => 'MarelloSupplierBundle:Supplier'];
    }

    /**
     * @param Supplier $supplier
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_supplier_supplier_view')]
    #[Template]
    #[AclAncestor('marello_supplier_view')]
    public function viewAction(Supplier $supplier)
    {
        return ['entity' => $supplier];
    }

    /**
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', methods: ['GET', 'POST'], name: 'marello_supplier_supplier_create')]
    #[Template]
    #[AclAncestor('marello_supplier_create')]
    public function createAction(Request $request)
    {
        return $this->update(new Supplier(), $request);
    }

    /**
     *
     * @param Supplier $supplier
     * @param Request $request
     * @return array
     */
    #[Route(path: '/update/{id}', methods: ['GET', 'POST'], requirements: ['id' => '\d+'], name: 'marello_supplier_supplier_update')]
    #[Template]
    #[AclAncestor('marello_supplier_update')]
    public function updateAction(Supplier $supplier, Request $request)
    {
        return $this->update($supplier, $request);
    }

    /**
     * Handles supplier updates and creation.
     *
     * @param Supplier|null $supplier
     * @param Request $request
     * @return array
     */
    protected function update(Supplier $supplier = null, Request $request)
    {
        return $this->container->get(UpdateHandlerFacade::class)->update(
            $supplier,
            $this->createForm(SupplierType::class, $supplier),
            $this->get(TranslatorInterface::class)->trans('marello.supplier.messages.success.supplier.saved'),
            $request
        );
    }

    /**
     * @param MarelloAddress $address
     * @return array
     */
    #[Route(path: '/widget/address/{id}/{typeId}', methods: ['GET', 'POST'], requirements: ['id' => '\d+', 'typeId' => '\d+'], name: 'marello_supplier_supplier_address')]
    #[Template('@MarelloSupplier/Supplier/widget/address.html.twig')]
    #[AclAncestor('marello_supplier_update')]
    public function addressAction(MarelloAddress $address)
    {
        return [
            'supplierAddress' => $address
        ];
    }

    /**
     *
     * @param Request $request
     * @param MarelloAddress $address
     * @return array
     */
    #[Route(path: '/update/address/{id}', methods: ['GET', 'POST'], requirements: ['id' => '\d+'], name: 'marello_supplier_supplier_updateaddress')]
    #[Template('@MarelloSupplier/Supplier/widget/updateAddress.html.twig')]
    #[AclAncestor('marello_supplier_update')]
    public function updateAddressAction(Request $request, MarelloAddress $address)
    {
        $responseData = array(
            'saved' => false,
        );
        $form  = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->container->get(ManagerRegistry::class)->getManager()->flush();
            $responseData['supplierAddress'] = $address;
            $responseData['saved'] = true;
        }

        $responseData['form'] = $form->createView();
        return $responseData;
    }

    #[Route(path: '/get-supplier-default-data', methods: ['GET'], name: 'marello_supplier_supplier_get_default_data')]
    #[AclAncestor('marello_supplier_view')] // {@inheritdoc}
    public function getSupplierDefaultDataAction(Request $request)
    {
        return new JsonResponse(
            $this->container->get(SupplierProvider::class)->getSupplierDefaultDataById(
                $request->query->get('supplier_id')
            )
        );
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                SupplierProvider::class,
                ManagerRegistry::class,
                UpdateHandlerFacade::class,
            ]
        );
    }
}
