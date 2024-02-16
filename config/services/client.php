<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use JoliCode\Elastically\Client;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\Result;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();

    $services->set(Client::class)
        ->args([
            '$config' => [
                'host' => env('MONSIEURBIZ_SEARCHPLUGIN_ES_HOST'),
                'port' => env('MONSIEURBIZ_SEARCHPLUGIN_ES_PORT'),
                'elastically_mappings_directory' => '%kernel.project_dir%/vendor/monsieurbiz/sylius-search-plugin/src/Resources/config/elasticsearch/mappings',
                'elastically_index_class_mapping' => [
                    'documents-it_it' => Result::class,
                    'documents-fr_fr' => Result::class,
                    'documents-fr' => Result::class,
                    'documents-en' => Result::class,
                    'documents-en_us' => Result::class,
                ],
                'elastically_bulk_size' => 100,
                'elastically_index_prefix' => env('MONSIEURBIZ_SEARCHPLUGIN_ES_PREFIX'),
            ]
        ])
    ;

};
