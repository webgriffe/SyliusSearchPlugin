<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MonsieurBiz\SyliusSearchPlugin\Applicator\CatalogPromotionApplicator;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\Index\Indexer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicatorInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(CatalogPromotionApplicator::class)
        ->decorate( CatalogPromotionApplicatorInterface::class)
        ->args([
            service('.inner'),
            service(Indexer::class),
        ])
    ;

};
