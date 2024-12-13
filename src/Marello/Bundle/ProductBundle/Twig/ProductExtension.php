<?php

namespace Marello\Bundle\ProductBundle\Twig;

use Marello\Bundle\CatalogBundle\Provider\CategoriesIdsProvider;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\CrosssellProduct;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\RelatedProduct;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\UpsellProduct;
use Marello\Bundle\ProductBundle\Entity\Repository\ProductRepository;
use Marello\Bundle\SalesBundle\Provider\ChannelProvider;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProductExtension extends AbstractExtension
{
    const NAME = 'marello_product';

    /** @var DoctrineHelper $doctrineHelper */
    private $doctrineHelper;

    /** @var TokenAccessorInterface $tokenAccessor */
    private $tokenAccessor;

    public function __construct(
        protected ChannelProvider $channelProvider,
        protected CategoriesIdsProvider $categoriesIdsProvider,
        protected AclHelper $aclHelper
    ) {
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'marello_sales_get_saleschannel_ids',
                [$this, 'getSalesChannelsIds']
            ),
            new TwigFunction(
                'marello_product_get_categories_ids',
                [$this, 'getCategoriesIds']
            ),
            new TwigFunction(
                'marello_get_product_by_sku',
                [$this, 'getProductBySku']
            ),
            new TwigFunction('marello_get_upsell_products_ids', [$this, 'getUpsellProductsIds']),
            new TwigFunction('marello_get_related_products_ids', [$this, 'getRelatedProductsIds']),
            new TwigFunction('marello_get_crosssell_products_ids', [$this, 'getCrosssellProductsIds']),
        ];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getSalesChannelsIds(Product $product)
    {
        return $this->channelProvider->getSalesChannelsIds($product);
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    public function getCategoriesIds(Product $product)
    {
        return $this->categoriesIdsProvider->getCategoriesIds($product);
    }

    /**
     * @param $sku
     * @return \Extend\Entity\EX_MarelloProductBundle_Product|Product|null
     */
    public function getProductBySku($sku)
    {
        if (!$this->doctrineHelper || !$sku) {
            return null;
        }

        $organization = null;
        if ($this->tokenAccessor) {
            $organization = $this->tokenAccessor->getOrganization();
        }

        /** @var ProductRepository $productRepository */
        $productRepository = $this->doctrineHelper->getEntityRepository(Product::class);
        return $productRepository->findOneBySku($sku, $this->aclHelper, $organization);
    }

    /**
     * @param Product $product
     *
     * @return int[]
     */
    public function getUpsellProductsIds(Product $product)
    {
        $qb = $this
            ->doctrineHelper
            ->getEntityManagerForClass(UpsellProduct::class)
            ->createQueryBuilder();

        $qb->select('DISTINCT IDENTITY(rp.relatedItem) as id')
            ->from(UpsellProduct::class, 'rp')
            ->where($qb->expr()->eq('rp.product', ':id'))
            ->setParameter('id', $product->getId())
            ->orderBy('rp.relatedItem');

        $productIds = $qb->getQuery()->getArrayResult();
        return array_column($productIds, 'id');
    }

    /**
     * @param Product $product
     *
     * @return int[]
     */
    public function getRelatedProductsIds(Product $product)
    {
        $qb = $this
            ->doctrineHelper
            ->getEntityManagerForClass(RelatedProduct::class)
            ->createQueryBuilder();

        $qb->select('DISTINCT IDENTITY(rp.relatedItem) as id')
            ->from(RelatedProduct::class, 'rp')
            ->where($qb->expr()->eq('rp.product', ':id'))
            ->setParameter('id', $product->getId())
            ->orderBy('rp.relatedItem');

        $productIds = $qb->getQuery()->getArrayResult();
        return array_column($productIds, 'id');
    }

    /**
     * @param Product $product
     *
     * @return int[]
     */
    public function getCrosssellProductsIds(Product $product)
    {
        $qb = $this
            ->doctrineHelper
            ->getEntityManagerForClass(CrosssellProduct::class)
            ->createQueryBuilder();

        $qb->select('DISTINCT IDENTITY(rp.relatedItem) as id')
            ->from(CrosssellProduct::class, 'rp')
            ->where($qb->expr()->eq('rp.product', ':id'))
            ->setParameter('id', $product->getId())
            ->orderBy('rp.relatedItem');

        $productIds = $qb->getQuery()->getArrayResult();
        return array_column($productIds, 'id');
    }

    /**
     * @param DoctrineHelper $doctrineHelper
     */
    public function setOroEntityDoctrineHelper(DoctrineHelper $doctrineHelper)
    {
        $this->doctrineHelper = $doctrineHelper;
    }

    /**
     * @param TokenAccessorInterface $tokenAccessor
     */
    public function setTokenAccessor(TokenAccessorInterface $tokenAccessor)
    {
        $this->tokenAccessor = $tokenAccessor;
    }
}
