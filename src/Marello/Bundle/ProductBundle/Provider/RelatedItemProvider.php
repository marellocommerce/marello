<?php

namespace Marello\Bundle\ProductBundle\Provider;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;

use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\UpsellProduct;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\RelatedProduct;
use Marello\Bundle\ProductBundle\DependencyInjection\Configuration;
use Marello\Bundle\ProductBundle\Entity\RelatedItem\CrosssellProduct;

class RelatedItemProvider
{
    /**
     * @param ConfigManager $configManager
     * @param DoctrineHelper $doctrineHelper
     */
    public function __construct(
        private ConfigManager $configManager,
        private DoctrineHelper $doctrineHelper
    ) {
    }

    public function getRelatedProductIds(Product $product)
    {
        $isBidirectional = $this
            ->configManager
            ->get(
                Configuration::getConfigKeyByName(Configuration::RELATED_PRODUCTS_BIDIRECTIONAL)
            );
        return $this->getRelatedItemIds($product, RelatedProduct::class, $isBidirectional);
    }

    /**
     * @param Product $product
     * @param string $relatedItemClass
     * @param bool $bidirectional
     * @return array
     */
    public function getRelatedItemIds(
        Product $product,
        string $relatedItemClass = RelatedProduct::class,
        bool $bidirectional = false
    ): array {
        $limit = $this->getLimitByRelatedItemClass($relatedItemClass);

        $qb = $this
            ->doctrineHelper
            ->getEntityManagerForClass($relatedItemClass)
            ->createQueryBuilder();

        $qb->select('DISTINCT IDENTITY(rp.relatedItem) as id')
            ->from($relatedItemClass, 'rp')
            ->where($qb->expr()->eq('rp.product', ':id'))
            ->setParameter('id', $product->getId())
            ->orderBy('rp.relatedItem');

        if ($limit) {
            $qb->setMaxResults($limit);
        }
        $productIds = $qb->getQuery()->getArrayResult();
        $productIds = array_column($productIds, 'id');
        if ($bidirectional && $relatedItemClass === RelatedProduct::class) {
            if ($limit === null || count($productIds) < $limit) {
                $qb = $this->doctrineHelper
                    ->getEntityManagerForClass($relatedItemClass)
                    ->createQueryBuilder()
                    ->select('DISTINCT IDENTITY(rp.product) as id')
                    ->from($relatedItemClass, 'rp')
                    ->where($qb->expr()->eq('rp.relatedItem', ':id'))
                    ->setParameter('id', $product->getId())
                    ->orderBy('rp.product');
                if ($productIds) {
                    $qb->andWhere($qb->expr()->notIn('rp.product', ':alreadySelectedIds'))
                        ->setParameter('alreadySelectedIds', $productIds);
                }
                if ($limit) {
                    $qb->setMaxResults($limit - count($productIds));
                }
                $biProductIds = $qb->getQuery()->getArrayResult();
                $biProductIds = array_column($biProductIds, 'id');
                $productIds = array_merge($productIds, $biProductIds);
            }
        }

        return $productIds;
    }

    /**
     * @param $relatedItemClass
     * @return int|null
     */
    public function getLimitByRelatedItemClass($relatedItemClass): ?int
    {
        $supportedClasses = $this->supportedClasses();
        if (!isset($supportedClasses[$relatedItemClass])) {
            return null;
        }

        $configKey = $supportedClasses[$relatedItemClass];
        return $this->configManager->get(Configuration::getConfigKeyByName($configKey));
    }

    /**
     * @return array
     */
    protected function supportedClasses(): array
    {
        return [
            RelatedProduct::class => Configuration::MAX_NUMBER_OF_RELATED_PRODUCTS,
            UpsellProduct::class => Configuration::MAX_NUMBER_OF_UPSELL_PRODUCTS,
            CrosssellProduct::class => Configuration::MAX_NUMBER_OF_CROSSSELL_PRODUCTS
        ];
    }
}
