<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Helper;

class FilterHelper
{
    public const MAIN_TAXON_FILTER = 'main_taxon';

    public const TAXON_FILTER = 'taxon';

    public const PRICE_FILTER = 'price';

    public static function buildFilters(array $appliedFilters): array
    {
        if (count($appliedFilters) === 0) {
            return [];
        }

        $filters = [];
        /**
         * @var string $field
         * @var array $values
         */
        foreach ($appliedFilters as $field => $values) {
            if (self::TAXON_FILTER === $field) {
                $filters[] = self::buildTaxonFilter($values);
            } elseif (self::MAIN_TAXON_FILTER === $field) {
                $filters[] = self::buildMainTaxonFilter($values);
            } elseif (self::PRICE_FILTER === $field) {
                if (isset($values['min'], $values['max'])) {
                    $filters[] = self::buildPriceFilter((int) $values['min'], (int) $values['max']);
                }
            } else {
                $filters[] = self::buildFilter($field, $values);
            }
        }

        return [
            'bool' => [
                'filter' => $filters,
            ],
        ];
    }

    public static function buildFilter(string $field, array $values): array
    {
        $filterValues = [];
        foreach ($values as $value) {
            $filterValues[] = self::buildFilterValue($value);
        }

        return [
            'nested' => [
                'path' => 'attributes',
                'query' => [
                    'bool' => [
                        'must' => [
                            'match' => [
                                'attributes.code' => $field,
                            ],
                        ],
                        'should' => $filterValues,
                        'minimum_should_match' => 1,
                    ],
                ],
            ],
        ];
    }

    public static function buildTaxonFilter(array $values): array
    {
        $filterValues = [];
        foreach ($values as $value) {
            $filterValues[] = self::buildTaxonFilterValue($value);
        }

        return [
            'nested' => [
                'path' => 'taxon',
                'query' => [
                    'bool' => [
                        'should' => $filterValues,
                        'minimum_should_match' => 1,
                    ],
                ],
            ],
        ];
    }

    public static function buildMainTaxonFilter(array $values): array
    {
        $filterValues = [];
        foreach ($values as $value) {
            $filterValues[] = self::buildMainTaxonFilterValue($value);
        }

        return [
            'nested' => [
                'path' => 'mainTaxon',
                'query' => [
                    'bool' => [
                        'should' => $filterValues,
                        'minimum_should_match' => 1,
                    ],
                ],
            ],
        ];
    }

    public static function buildPriceFilter(int $min, int $max): array
    {
        return [
            'nested' => [
                'path' => 'price',
                'query' => [
                    'range' => [
                        'price.value' => [
                            [
                                'gte' => $min * 100,
                                'lte' => $max * 100,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function buildFilterValue(string $value): array
    {
        return [
            'term' => [
                'attributes.value.keyword' => SlugHelper::toLabel($value),
            ],
        ];
    }

    public static function buildTaxonFilterValue(string $value): array
    {
        return [
            'term' => [
                'taxon.name' => SlugHelper::toLabel($value),
            ],
        ];
    }

    public static function buildMainTaxonFilterValue(string $value): array
    {
        return [
            'term' => [
                'mainTaxon.name' => SlugHelper::toLabel($value),
            ],
        ];
    }
}
