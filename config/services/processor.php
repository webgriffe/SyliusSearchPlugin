<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MonsieurBiz\SyliusSearchPlugin\Model\Document\Index\Indexer;
use MonsieurBiz\SyliusSearchPlugin\Processor\CatalogPromotionStateProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(CatalogPromotionStateProcessor::class)
        ->decorate(CatalogPromotionStateProcessorInterface::class)
        ->args([
            service('.inner'),
            service(Indexer::class),
        ])
    ;
};
