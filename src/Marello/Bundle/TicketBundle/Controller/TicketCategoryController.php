<?php

namespace Marello\Bundle\TicketBundle\Controller;

use Marello\Bundle\TicketBundle\Entity\TicketCategory;
use Marello\Bundle\TicketBundle\Form\Type\TicketCategoryType;
use Oro\Bundle\FormBundle\Model\UpdateHandlerFacade;
use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Marello\Bundle\TicketBundle\Entity\TicketCategory as CategoryAlias;
use Symfony\Contracts\Translation\TranslatorInterface;

class TicketCategoryController extends AbstractController
{
    /**
     * @Route("/", name="marello_ticket_category_index")
     * @Template
     * @AclAncestor("marello_ticket_category_view")
     *
     * @return array
     */
    public function indexAction()
    {
        return ['entity_class' => TicketCategory::class];
    }

    /**
     * @Route(path="/view/{id}", name="marello_ticket_category_view", requirements={"id"="\d+"})
     * @Template
     */
    public function viewAction(CategoryAlias $category)
    {
        return [
            'entity' => $category
        ];
    }

    /**
     * @Route(
     *     path="/create",
     *     name="marello_ticket_category_create",
     *     options={"expose"=true}
     * )
     * @Template
     * @Acl(
     *       id="marello_ticket_category_create",
     *       type="entity",
     *       class="Marello\TicketBundle\Entity\TicketCategory",
     *       permission="CREATE"
     *  )
     */
    public function createAction(Request $request): array|RedirectResponse
    {
        $createMessage = $this->get(TranslatorInterface::class)->trans(
            'marello.saved.message'
        );

        return $this->update(new TicketCategory(), $request, $createMessage);
    }
//
//    /**
//     * @Route(
//     *     path="/update/{id}",
//     *     name="marello_ticket_category_update",
//     *     requirements={"id"="\d+"}
//     * )
//     * @Template
//     * (
//     *      id="marello_ticket_category_update",
//     *      type="entity",
//     *      class="Marello\TicketBundle\Entity\TicketCategory",
//     *      permission="EDIT"
//     * )
//     */
//    public function updateAction(TicketCategory $entity, Request $request): array|RedirectResponse
//    {
//        $createMessage = $this->get(TranslatorInterface::class)->trans(
//            'marello.saved.message'
//        );
//
//        return $this->update($entity, $request, $createMessage);
//    }
//
    protected function update(
        TicketCategory $entity,
        Request        $request,
        string         $message = ''
    ): array|RedirectResponse
    {
        return $this->get(UpdateHandlerFacade::class)->update(
            $entity,
            $this->createForm(TicketCategoryType::class, $entity),
            $message,
            $request,
            null
        );
    }

    public static function getSubscribedServices()
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