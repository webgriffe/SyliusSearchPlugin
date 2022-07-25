<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Applicator;

use MonsieurBiz\SyliusSearchPlugin\Model\Document\Index\Indexer;
use MonsieurBiz\SyliusSearchPlugin\Model\Documentable\DocumentableInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CatalogPromotionApplicator implements CatalogPromotionApplicatorInterface
{
    public function __construct(
        private CatalogPromotionApplicatorInterface $decoratedCatalogPromotionApplicator,
        private Indexer $indexer,
    ) {
    }

    public function applyOnVariant(ProductVariantInterface $variant, CatalogPromotionInterface $catalogPromotion): void
    {
        $this->decoratedCatalogPromotionApplicator->applyOnVariant($variant, $catalogPromotion);
        $product = $variant->getProduct();
        if (!$product instanceof DocumentableInterface) {
            return;
        }
        $this->indexer->indexOne($product);
    }
}
