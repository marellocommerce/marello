<?php

namespace Marello\Bundle\DigitalAssetBundle\Controller;

use Marello\Bundle\DigitalAssetBundle\Entity\DigitalAssetCategory;
use Marello\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetCategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;

class DigitalAssetCategoryController extends AbstractController
{
    #[Route(path: '/', name: 'marello_digital_asset_category_index')]
    #[Template]
    #[AclAncestor('marello_digital_asset_category_view')]
    public function indexAction(): array
    {
        return ['entity_class' => DigitalAssetCategory::class];
    }

    #[Route(path: '/view/{id}', name: 'marello_digital_asset_category_view', requirements: ['id' => '\d+'])]
    #[Template]
    #[Acl(id: 'marello_digital_asset_category_view', type: 'entity', class: DigitalAssetCategory::class, permission: 'VIEW')]
    public function viewAction(DigitalAssetCategory $category)
    {
        return ['entity' => $category];
    }

    #[Route(path: '/create', name: 'marello_digital_asset_category_create', methods: ['GET', 'POST'])]
    #[Template('@MarelloDigitalAsset/DigitalAssetCategory/update.html.twig')]
    #[Acl(id: 'marello_digital_asset_category_create', type: 'entity', class: DigitalAssetCategory::class, permission: 'CREATE')]
    public function createAction(Request $request): array|RedirectResponse
    {
        $createMessage = $this->container->get(TranslatorInterface::class)->trans(
            'marello.digitalasset.category.saved.message'
        );

        return $this->update(new DigitalAssetCategory(), $request, $createMessage);
    }

    #[Route(
        path: '/update/{id}',
        name: 'marello_digital_asset_category_update',
        requirements: ['id' => '\d+'],
        options: ['expose' => true],
        methods: ['GET', 'POST', 'PATCH']
    )]
    #[Template('@MarelloDigitalAsset/DigitalAssetCategory/update.html.twig')]
    #[Acl(id: 'marello_digital_asset_category_update', type: 'entity', class: DigitalAssetCategory::class, permission: 'EDIT')]
    public function updateAction(DigitalAssetCategory $entity, Request $request): array|RedirectResponse
    {
        $createMessage = $this->container->get(TranslatorInterface::class)->trans(
            'marello.digitalasset.category.saved.message'
        );

        return $this->update($entity, $request, $createMessage);
    }

    protected function update(
        DigitalAssetCategory $entity,
        Request $request,
        string $message = ''
    ): array|RedirectResponse {
        return $this->container->get(UpdateHandlerFacade::class)->update(
            $entity,
            $this->createForm(DigitalAssetCategoryType::class, $entity),
            $message,
            $request,
            null
        );
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                TranslatorInterface::class,
                UpdateHandlerFacade::class,
            ]
        );
    }
}