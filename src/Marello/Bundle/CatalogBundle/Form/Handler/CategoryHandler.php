<?php

namespace Marello\Bundle\CatalogBundle\Form\Handler;

use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Marello\Bundle\CatalogBundle\Entity\Category;
use Marello\Bundle\CustomerBundle\Entity\Company;
use Marello\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\FormBundle\Form\Handler\RequestHandlerTrait;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CategoryHandler
{
    use RequestHandlerTrait;

    /** @var FormInterface */
    protected $form;

    /** @var Request */
    protected $request;

    /** @var EntityManager */
    protected $manager;

    /**
     * @param FormInterface $form
     * @param RequestStack  $requestStack
     * @param ObjectManager $manager
     */
    public function __construct(
        FormInterface $form,
        RequestStack  $requestStack,
        ObjectManager $manager
    ) {
        $this->form = $form;
        $this->request = $requestStack->getCurrentRequest();
        $this->manager = $manager;
    }

    /**
     * @param Category $category
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process(Category $category)
    {
        $this->form->setData($category);

        if (in_array($this->request->getMethod(), ['POST', 'PUT'])) {
            $this->submitPostPutRequest($this->form, $this->request);
            if ($this->form->isValid()) {
                $appendProducts = $this->form->get('appendProducts')->getData();
                $removeProducts = $this->form->get('removeProducts')->getData();

                $appendCompanies = array_filter((array) $this->form->get('appendCompanies')->getData());
                $removeCompanies = array_filter((array) $this->form->get('removeCompanies')->getData());

                $this->onSuccess($category, $appendProducts, $removeProducts, $appendCompanies, $removeCompanies);

                return true;
            }
        }

        return false;
    }

    /**
     * @param Category $category
     * @param Product[] $appendProducts
     * @param Product[] $removeProducts
     * @param Company[] $appendCompanies
     * @param Company[] $removeCompanies
     */
    protected function onSuccess(
        Category $category,
        array $appendProducts,
        array $removeProducts,
        array $appendCompanies,
        array $removeCompanies
    ) {
        $this->appendProducts($category, $appendProducts);
        $this->removeProducts($category, $removeProducts);

        $this->appendCompanies($category, $appendCompanies);
        $this->removeCompanies($category, $removeCompanies);

        $this->manager->persist($category);
        $this->manager->flush();
    }

    /**
     * @param Category $category
     * @param Product[] $products
     */
    protected function appendProducts(Category $category, array $products)
    {
        /** @var $product Product */
        foreach ($products as $product) {
            $category->addProduct($product);
        }
    }

    /**
     * @param Category $category
     * @param Product[] $products
     */
    protected function removeProducts(Category $category, array $products)
    {
        /** @var $product Product */
        foreach ($products as $product) {
            $category->removeProduct($product);
        }
    }

    /**
     * @param Category $category
     * @param Company[] $companies
     */
    protected function appendCompanies(Category $category, array $companies)
    {
        foreach ($companies as $company) {
            $category->addCompany($company);
        }
    }

    /**
     * @param Category $category
     * @param Company[] $companies
     */
    protected function removeCompanies(Category $category, array $companies)
    {
        foreach ($companies as $company) {
            $category->removeCompany($company);
        }
    }
    
    /**
     * Returns form instance
     *
     * @return FormView
     */
    public function getFormView()
    {
        return $this->form->createView();
    }
}
