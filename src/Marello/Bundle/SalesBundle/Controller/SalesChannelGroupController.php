<?php

namespace Marello\Bundle\SalesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;

use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;
use Marello\Bundle\SalesBundle\Form\Type\SalesChannelGroupType;

class SalesChannelGroupController extends AbstractController
{
    /**
     * @return array
     */
    #[Route(path: '/', name: 'marello_sales_saleschannelgroup_index')]
    #[Template]
    #[AclAncestor('marello_sales_saleschannelgroup_view')]
    public function indexAction()
    {
        return [
            'entity_class' => SalesChannelGroup::class
        ];
    }

    /**
     *
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', name: 'marello_sales_saleschannelgroup_create')]
    #[Template('@MarelloSales/SalesChannelGroup/update.html.twig')]
    #[Acl(id: 'marello_sales_saleschannelgroup_create', type: 'entity', permission: 'CREATE', class: SalesChannelGroup::class)]
    public function createAction(Request $request)
    {
        return $this->update(new SalesChannelGroup(), $request);
    }

    /**
     *
     * @param SalesChannelGroup $salesChannelGroup
     * @return array
     */
    #[Route(path: '/view/{id}', name: 'marello_sales_saleschannelgroup_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'marello_sales_saleschannelgroup_view', type: 'entity', class: SalesChannelGroup::class, permission: 'VIEW')]
    public function viewAction(SalesChannelGroup $salesChannelGroup)
    {
        return [
            'entity' => $salesChannelGroup,
        ];
    }

    /**
     * @param Request $request
     * @param SalesChannelGroup $entity
     * @return array
     */
    #[Route(path: '/update/{id}', name: 'marello_sales_saleschannelgroup_update', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'marello_sales_saleschannelgroup_update', type: 'entity', permission: 'EDIT', class: SalesChannelGroup::class)]
    public function updateAction(Request $request, SalesChannelGroup $entity)
    {
        if ($entity->isSystem()) {
            $this->addFlash(
                'warning',
                'marello.sales.saleschannelgroup.messages.warning.is_system_update_attempt'
            );

            return $this->redirect($this->generateUrl('marello_sales_saleschannelgroup_index'));
        }

        return $this->update($entity, $request);
    }

    /**
     * @param SalesChannelGroup $entity
     * @param Request $request
     * @return array|RedirectResponse
     */
    protected function update(SalesChannelGroup $entity, Request $request)
    {
        return $this->container->get(UpdateHandlerFacade::class)->update(
            $entity,
            $this->createForm(SalesChannelGroupType::class, $entity),
            $this->container->get(TranslatorInterface::class)->trans('marello.sales.saleschannelgroup.messages.success.saved'),
            $request,
            'marello_sales.saleschannelgroup_form.handler'
        );
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                UpdateHandlerFacade::class,
                TranslatorInterface::class,
            ]
        );
    }
}
