<?php

namespace Marello\Bundle\SalesBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;

use Marello\Bundle\SalesBundle\Entity\SalesChannel;
use Marello\Bundle\SalesBundle\Form\Type\SalesChannelType;

class SalesChannelController extends AbstractController
{
    #[Route(path: '/', name: 'marello_sales_saleschannel_index', methods: ['GET'])]
    #[Template]
    #[AclAncestor('marello_sales_saleschannel_view')]
    public function indexAction()
    {
        return [
            'entity_class' => SalesChannel::class,
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', name: 'marello_sales_saleschannel_create', methods: ['GET', 'POST'])]
    #[Template('@MarelloSales/SalesChannel/update.html.twig')]
    #[AclAncestor('marello_saleschannel_create')]
    public function createAction(Request $request)
    {
        return $this->update(new SalesChannel(), $request);
    }
    
    /**
     *
     * @param SalesChannel $salesChannel
     * @return array
     */
    #[Route(path: '/view/{id}', name: 'marello_sales_saleschannel_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'marello_sales_saleschannel_view', type: 'entity', class: SalesChannel::class, permission: 'VIEW')]
    public function viewAction(SalesChannel $salesChannel)
    {
        return [
            'entity' => $salesChannel,
        ];
    }

    /**
     *
     * @param Request $request
     * @param SalesChannel $channel
     * @return array
     */
    #[Route(
        path: '/update/{id}',
        name: 'marello_sales_saleschannel_update',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST']
    )]
    #[Template]
    #[AclAncestor('marello_saleschannel_update')]
    public function updateAction(SalesChannel $channel, Request $request)
    {
        return $this->update($channel, $request);
    }

    /**
     * @param SalesChannel $channel
     * @param Request $request
     * @return array|RedirectResponse
     */
    protected function update(SalesChannel $channel, Request $request)
    {
        return $this->container->get(UpdateHandlerFacade::class)->update(
            $channel,
            $this->createForm(SalesChannelType::class, $channel),
            $this->container
                ->get(TranslatorInterface::class)->trans('marello.sales.saleschannel.messages.success.saved'),
            $request,
            'marello_sales.saleschannel_form.handler'
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
