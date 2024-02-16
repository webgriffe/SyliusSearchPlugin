<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MonsieurBiz\SyliusSearchPlugin\Provider\DocumentRepositoryProvider;
use MonsieurBiz\SyliusSearchPlugin\Provider\SearchQueryProvider;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(SearchQueryProvider::class)
        ->args([
            param('monsieurbiz_sylius_search.files'),
        ])
    ;

    $services->set(DocumentRepositoryProvider::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            param('monsieurbiz_sylius_search.documentable_classes'),
        ])
    ;
};
