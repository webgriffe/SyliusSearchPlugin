<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Helper;

class SortHelper
{
    public static function getSortParamByField(string $field, string $channel, string $order = 'asc', string $taxon = ''): array
    {
        return match ($field) {
            'name' => self::buildSort('attributes.value.keyword', $order, 'attributes', 'attributes.code', $field),
            'created_at' => self::buildSort('attributes.value.keyword', $order, 'attributes', 'attributes.code', $field),
            'price' => self::buildSort('price.value', $order, 'price', 'price.channel', $channel),
            'position' => self::buildSort('taxon.productPosition', $order, 'taxon', 'taxon.code', $taxon),
            // Dummy value to have null sorting in ES and keep ES results sorting
            default => self::buildSort('attributes.value.keyword', $order, 'attributes', 'attributes.code', 'dummy'),
        };
    }

    public static function buildSort(
        string $field,
        string $order,
        string $nestedPath,
        string $sortFilterField,
        string $sortFilterValue,
    ): array {
        return [
            $field => [
                'order' => $order,
                'nested' => [
                    'path' => $nestedPath,
                    'filter' => [
                        'term' => [$sortFilterField => $sortFilterValue],
                    ],
                ],
            ],
        ];
    }
}
