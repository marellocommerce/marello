<?php

namespace Marello\Bundle\ProductBundle\Form\EventListener;

use Twig\Environment;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Oro\Bundle\UIBundle\View\ScrollData;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;

use Marello\Bundle\ProductBundle\Provider\RelatedItemProvider;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\UpsellProduct;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\RelatedProduct;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\CrosssellProduct;

/**
 * Adds related product information (tabs, grids, forms) to the product edit page.
 */
class RelatedItemsProductUpdateListener
{
    const RELATED_ITEMS_ID = 'relatedItems';

    /** @var int */
    const BLOCK_PRIORITY = 1500;

    /**
     * @param TranslatorInterface               $translator
     * @param AuthorizationCheckerInterface     $authorizationChecker
     */
    public function __construct(
        private TranslatorInterface $translator,
        private AuthorizationCheckerInterface $authorizationChecker,
        private RelatedItemProvider $relatedItemProvider
    ) {
    }

    /**
     * @param BeforeListRenderEvent $event
     */
    public function onProductEdit(BeforeListRenderEvent $event)
    {
        $twigEnv = $event->getEnvironment();
        $tabs = [];
        $grids = [];

        if ($this->authorizationChecker->isGranted('marello_product_update')) {
            $tabs[] = [
                'id' => 'related-products-block',
                'label' => $this->translator->trans('marello.product.sections.tabs.related_products')
            ];
            $grids[] = $this->getRelatedProductsEditBlock($event, $twigEnv);
        }

        if ($this->authorizationChecker->isGranted('marello_product_update')) {
            $tabs[] = [
                'id' => 'upsell-products-block',
                'label' => $this->translator->trans('marello.product.sections.tabs.upsell_products')
            ];
            $grids[] = $this->getUpsellProductsEditBlock($event, $twigEnv);
        }

        if ($this->authorizationChecker->isGranted('marello_product_update')) {
            $tabs[] = [
                'id' => 'crosssell-products-block',
                'label' => $this->translator->trans('marello.product.sections.tabs.crosssell_products')
            ];
            $grids[] = $this->getCrosssellProductsEditBlock($event, $twigEnv);
        }

        if (count($tabs) > 1) {
            $grids = array_merge([$this->renderTabs($twigEnv, $tabs)], $grids);
        }

        if (count($grids) > 0) {
            $this->addEditPageBlock($event->getScrollData(), $grids);
        }
    }

    /**
     * @param ScrollData $scrollData
     * @param string[] $htmlBlocks
     */
    private function addEditPageBlock(ScrollData $scrollData, array $htmlBlocks)
    {
        $scrollData->addNamedBlock(
            self::RELATED_ITEMS_ID,
            $this->translator->trans('marello.product.sections.related_items'),
            self::BLOCK_PRIORITY
        );

        $subBlock = $scrollData->addSubBlock(self::RELATED_ITEMS_ID);
        $scrollData->addSubBlockData(
            self::RELATED_ITEMS_ID,
            $subBlock,
            implode('', $htmlBlocks),
            'relatedItems'
        );
    }

    /**
     * @param BeforeListRenderEvent $event
     * @param Environment $twigEnv
     * @return string
     */
    private function getRelatedProductsEditBlock(BeforeListRenderEvent $event, Environment $twigEnv)
    {
        return $twigEnv->render(
            '@MarelloProduct/Product/RelatedItems/relatedProducts.html.twig',
            [
                'form' => $event->getFormView(),
                'entity' => $event->getEntity(),
                'itemsLimit' => $this->relatedItemProvider->getLimitByRelatedItemClass(RelatedProduct::class)
            ]
        );
    }

    /**
     * @param BeforeListRenderEvent $event
     * @param Environment $twigEnv
     * @return string
     */
    private function getUpsellProductsEditBlock(BeforeListRenderEvent $event, Environment $twigEnv)
    {
        var_dump($this->relatedItemProvider->getLimitByRelatedItemClass(UpsellProduct::class));
        return $twigEnv->render(
            '@MarelloProduct/Product/RelatedItems/upsellProducts.html.twig',
            [
                'form' => $event->getFormView(),
                'entity' => $event->getEntity(),
                'itemsLimit' => $this->relatedItemProvider->getLimitByRelatedItemClass(UpsellProduct::class)
            ]
        );
    }

    /**
     * @param BeforeListRenderEvent $event
     * @param Environment $twigEnv
     * @return string
     */
    private function getCrosssellProductsEditBlock(BeforeListRenderEvent $event, Environment $twigEnv)
    {
        return $twigEnv->render(
            '@MarelloProduct/Product/RelatedItems/crosssellProducts.html.twig',
            [
                'form' => $event->getFormView(),
                'entity' => $event->getEntity(),
                'itemsLimit' => $this->relatedItemProvider->getLimitByRelatedItemClass(CrosssellProduct::class)
            ]
        );
    }

    /**
     * @param Environment $twigEnv
     * @param array $tabs
     * @return string
     */
    private function renderTabs(Environment $twigEnv, array $tabs)
    {
        return $twigEnv->render(
            '@MarelloProduct/Product/RelatedItems/tabs.html.twig',
            [
                'relatedItemsTabsItems' => $tabs
            ]
        );
    }
}
