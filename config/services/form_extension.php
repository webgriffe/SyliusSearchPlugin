<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MonsieurBiz\SyliusSearchPlugin\Form\Extension\ProductAttributeTypeExtension;
use MonsieurBiz\SyliusSearchPlugin\Form\Extension\ProductOptionTypeExtension;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(ProductAttributeTypeExtension::class)
        ->tag('form.type_extension')
    ;

    $services->set(ProductOptionTypeExtension::class)
        ->tag('form.type_extension')
    ;
};
