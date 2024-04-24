<?php

namespace Marello\Bundle\ShippingBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\UIBundle\Route\Router;
use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionDispatcher;

use Marello\Bundle\ShippingBundle\Entity\ShippingMethodsConfigsRule;
use Marello\Bundle\ShippingBundle\Form\Type\ShippingMethodsConfigsRuleType;
use Marello\Bundle\ShippingBundle\Form\Handler\ShippingMethodsConfigsRuleHandler;

class ShippingMethodsConfigsRuleController extends AbstractController
{
    /**
     * @return array
     */
    #[Route(path: '/', name: 'marello_shipping_methods_configs_rule_index')]
    #[Template]
    #[AclAncestor('marello_shipping_methods_configs_rule_view')]
    public function indexAction()
    {
        return [
            'entity_class' => ShippingMethodsConfigsRule::class
        ];
    }

    /**
     *
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', name: 'marello_shipping_methods_configs_rule_create')]
    #[Template('@MarelloShipping/ShippingMethodsConfigsRule/update.html.twig')]
    #[Acl(id: 'marello_shipping_methods_configs_rule_create', type: 'entity', permission: 'CREATE', class: ShippingMethodsConfigsRule::class)]
    public function createAction(Request $request)
    {
        return $this->update(new ShippingMethodsConfigsRule(), $request);
    }

    /**
     *
     * @param ShippingMethodsConfigsRule $shippingRule
     * @return array
     */
    #[Route(path: '/view/{id}', name: 'marello_shipping_methods_configs_rule_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'marello_shipping_methods_configs_rule_view', type: 'entity', class: ShippingMethodsConfigsRule::class, permission: 'VIEW')]
    public function viewAction(ShippingMethodsConfigsRule $shippingRule)
    {
        return [
            'entity' => $shippingRule,
        ];
    }

    /**
     * @param Request $request
     * @param ShippingMethodsConfigsRule $entity
     * @return array
     */
    #[Route(path: '/update/{id}', name: 'marello_shipping_methods_configs_rule_update', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'marello_shipping_methods_configs_rule_update', type: 'entity', permission: 'EDIT', class: ShippingMethodsConfigsRule::class)]
    public function updateAction(Request $request, ShippingMethodsConfigsRule $entity)
    {
        return $this->update($entity, $request);
    }

    /**
     * @param ShippingMethodsConfigsRule $entity
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function update(ShippingMethodsConfigsRule $entity, Request $request)
    {
        $form = $this->createForm(ShippingMethodsConfigsRuleType::class);
        if ($this->container->get(ShippingMethodsConfigsRuleHandler::class)->process($form, $entity)) {
            $request->getSession()->getFlashBag()->add(
                'success',
                $this->container->get(TranslatorInterface::class)->trans('marello.shipping.controller.rule.saved.message')
            );

            return $this->container->get(Router::class)->redirect($entity);
        }

        if ($request->get(ShippingMethodsConfigsRuleHandler::UPDATE_FLAG, false)) {
            // take different form due to JS validation should be shown even in case
            // when it was not validated on backend
            $form = $this->createForm(ShippingMethodsConfigsRuleType::class, $form->getData());
        }

        return [
            'entity' => $entity,
            'form'   => $form->createView()
        ];
    }

    /**
     * @param string $gridName
     * @param string $actionName
     * @param Request $request
     * @return JsonResponse
     */
    #[Route(path: '/{gridName}/massAction/{actionName}', name: 'marello_status_shipping_rule_massaction')]
    #[Acl(id: 'marello_shipping_methods_configs_rule_update', type: 'entity', permission: 'EDIT', class: ShippingMethodsConfigsRule::class)]
    public function markMassAction($gridName, $actionName, Request $request)
    {
        /** @var MassActionDispatcher $massActionDispatcher */
        $massActionDispatcher = $this->container->get(MassActionDispatcher::class);

        $response = $massActionDispatcher->dispatchByRequest($gridName, $actionName, $request);

        $data = [
            'successful' => $response->isSuccessful(),
            'message' => $response->getMessage()
        ];

        return new JsonResponse(array_merge($data, $response->getOptions()));
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                ShippingMethodsConfigsRuleHandler::class,
                TranslatorInterface::class,
                Router::class,
                MassActionDispatcher::class,
            ]
        );
    }
}
