<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MonsieurBiz\SyliusSearchPlugin\Helper\RenderDocumentUrlHelper;
use MonsieurBiz\SyliusSearchPlugin\Model\Config\GridConfig;
use MonsieurBiz\SyliusSearchPlugin\Twig\Extension\CheckMethodExists;
use MonsieurBiz\SyliusSearchPlugin\Twig\Extension\RenderDocumentUrl;
use MonsieurBiz\SyliusSearchPlugin\Twig\Extension\RenderSearchForm;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(GridConfig::class)
        ->args([
            param('monsieurbiz_sylius_search.grid'),
            service('sylius.repository.product_attribute'),
            service('sylius.repository.product_option'),
        ])
    ;

    $services->set(CheckMethodExists::class)
        ->args([
            service('service_container'),
        ])
        ->tag('twig.extension')
    ;

    $services->set(RenderDocumentUrl::class)
        ->args([
            service(RenderDocumentUrlHelper::class),
        ])
        ->tag('twig.extension')
    ;

    $services->set(RenderSearchForm::class)
        ->args([
            service('form.factory'),
            service('twig'),
            service('request_stack'),
        ])
        ->tag('twig.extension')
    ;
};
