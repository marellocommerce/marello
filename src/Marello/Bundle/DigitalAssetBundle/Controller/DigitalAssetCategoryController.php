<?php

namespace Marello\Bundle\DigitalAssetBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Oro\Bundle\SecurityBundle\Attribute\Acl;
use Oro\Bundle\SecurityBundle\Attribute\AclAncestor;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Attribute\CsrfProtection;

use Marello\Bundle\DigitalAssetBundle\Entity\DigitalAssetCategory;
use Marello\Bundle\DigitalAssetBundle\Form\Type\DigitalAssetCategoryType;

class DigitalAssetCategoryController extends AbstractController
{
    #[Route(path: '/create', name: 'marello_digital_asset_category_create', methods: ['GET', 'POST'])]
    #[Template('@MarelloDigitalAsset/DigitalAssetCategory/update.html.twig')]
    #[Acl(id: 'marello_digital_asset_category_create', type: 'entity', class: DigitalAssetCategory::class, permission: 'CREATE')]
    public function createAction(Request $request): array|RedirectResponse
    {
        $createMessage = $this->container->get(TranslatorInterface::class)->trans(
            'marello.digitalasset.digitalassetcategory.saved.message'
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
            'marello.digitalasset.digitalassetcategory.saved.message'
        );

        return $this->update($entity, $request, $createMessage);
    }

    #[Route(
        path: '/delete/{id}',
        name: 'marello_digital_asset_category_delete',
        requirements: ['id' => '\d+'],
        methods: ['DELETE']
    )]
    #[AclAncestor('marello_digital_asset_category_delete')]
    #[CsrfProtection]
    public function deleteAction(DigitalAssetCategory $entity): JsonResponse
    {
        $translator = $this->container->get(TranslatorInterface::class);
        if ($this->isGranted('delete', $entity)) {
            $registry = $this->container->get(ManagerRegistry::class);
            $entityManager = $registry->getManagerForClass(DigitalAssetCategory::class);
            $entityManager->remove($entity);
            $entityManager->flush();

            $successful = true;
            $message = $translator->trans('oro.action.delete_message');
        } else {
            $successful = false;
            $message = $translator->trans('oro.action.delete_message');
        }

        return new JsonResponse(['message' => $message, 'successful' => $successful]);
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
                ManagerRegistry::class
            ]
        );
    }
}
