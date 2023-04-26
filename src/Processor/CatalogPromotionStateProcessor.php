<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Processor;

use MonsieurBiz\SyliusSearchPlugin\Model\Document\Index\Indexer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;

final class CatalogPromotionStateProcessor implements CatalogPromotionStateProcessorInterface
{
    public function __construct(
        private CatalogPromotionStateProcessorInterface $decoratedCatalogPromotionStateProcessor,
        private Indexer $indexer,
    ) {
    }

    public function process(CatalogPromotionInterface $catalogPromotion): void
    {
        $this->decoratedCatalogPromotionStateProcessor->process($catalogPromotion);
        $this->indexer->indexAll();
    }
}
