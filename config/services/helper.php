<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use MonsieurBiz\SyliusSearchPlugin\Helper\RenderDocumentUrlHelper;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(RenderDocumentUrlHelper::class);
};
