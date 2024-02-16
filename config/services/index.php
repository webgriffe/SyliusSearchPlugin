<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use JoliCode\Elastically\Client;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\Index\Indexer;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\Index\Search;
use MonsieurBiz\SyliusSearchPlugin\Provider\DocumentRepositoryProvider;
use MonsieurBiz\SyliusSearchPlugin\Provider\SearchQueryProvider;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(Search::class)
        ->args([
            service(Client::class),
            service(SearchQueryProvider::class),
            service('sylius.context.channel'),
            service('logger'),
        ])
    ;

    $services->set(Indexer::class)
        ->args([
            service(Client::class),
            service(DocumentRepositoryProvider::class),
            service('sylius.repository.locale'),
        ])
    ;
};
