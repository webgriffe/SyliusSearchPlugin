<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MonsieurBiz\SyliusSearchPlugin\Context\RequestTaxonContext;
use MonsieurBiz\SyliusSearchPlugin\Context\TaxonContextInterface;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(TaxonContextInterface::class, RequestTaxonContext::class)
        ->args([
            service('request_stack'),
            service('sylius.repository.taxon'),
            service('sylius.context.locale'),
        ])
    ;
};
