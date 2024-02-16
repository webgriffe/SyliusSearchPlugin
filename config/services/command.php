<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MonsieurBiz\SyliusSearchPlugin\Command\PopulateCommand;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\Index\Indexer;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(PopulateCommand::class)
        ->args([
            service(Indexer::class),
        ])
        ->tag('console.command')
    ;
};
