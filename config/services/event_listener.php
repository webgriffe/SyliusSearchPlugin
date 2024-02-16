<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MonsieurBiz\SyliusSearchPlugin\EventListener\DocumentListener;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\Index\Indexer;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(DocumentListener::class)
        ->args([
            service(Indexer::class),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.post_create', 'method' => 'saveDocument'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.post_update', 'method' => 'saveDocument'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.pre_delete', 'method' => 'deleteDocument'])
    ;
};
