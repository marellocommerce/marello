<?php

namespace Marello\Bundle\CatalogBundle\Controller;

use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\CatalogBundle\Form\Handler\CategoryHandler;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\UIBundle\Route\Router;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryController extends AbstractController
{
    /**
     * @AclAncestor("marello_category_view")
     * @Template
     */
    #[Route(path: '/', name: 'marello_category_index')]
    public function indexAction()
    {
        return ['entity_class' => 'MarelloCatalogBundle:Category'];
    }

    /**
     * @AclAncestor("marello_category_create")
     * @Template("@MarelloCatalog/Category/update.html.twig")
     *
     * @param Request $request
     * @return array
     */
    #[Route(path: '/create', name: 'marello_category_create')]
    public function createAction(Request $request)
    {
        return $this->update(new Category(), $request);
    }

    /**
     * @AclAncestor("marello_category_update")
     * @Template("@MarelloCatalog/Category/update.html.twig")
     *
     * @param Category $category
     * @param Request $request
     * @return array
     */
    #[Route(path: '/update/{id}', requirements: ['id' => '\d+'], name: 'marello_category_update')]
    public function updateAction(Category $category, Request $request)
    {
        return $this->update($category, $request);
    }

    /**
     * @param Category $category
     * @param Request $request
     * @return array
     */
    protected function update(Category $category, Request $request)
    {
        $handler = $this->container->get(CategoryHandler::class);

        if ($handler->process($category)) {
            $request->getSession()->getFlashBag()->add(
                'success',
                $this->container->get(TranslatorInterface::class)->trans('marello.catalog.ui.category.saved.message')
            );

            return $this->container->get(Router::class)->redirect($category);
        }

        return [
            'entity' => $category,
            'form'   => $handler->getFormView(),
        ];
    }

    /**
     * @AclAncestor("marello_category_view")
     * @Template("@MarelloCatalog/Category/view.html.twig")
     *
     * @param Category $category
     * @return array
     */
    #[Route(path: '/view/{id}', requirements: ['id' => '\d+'], name: 'marello_category_view')]
    public function viewAction(Category $category)
    {
        return [
            'entity' => $category,
        ];
    }

    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                CategoryHandler::class,
                TranslatorInterface::class,
                Router::class,
            ]
        );
    }
}
