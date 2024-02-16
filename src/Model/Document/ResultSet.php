<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Document;

use Elastica\ResultSet as ElasticaResultSet;
use JoliCode\Elastically\Result;
use MonsieurBiz\SyliusSearchPlugin\Adapter\ResultSetAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Component\Core\Model\TaxonInterface;
use function count;

class ResultSet
{
    /** @var ResultInterface[] */
    private array $results = [];

    private int $totalHits;

    /** @var Filter[] */
    private array $filters = [];

    private ?RangeFilter $priceFilter = null;

    private ?Filter $taxonFilter = null;

    private ?Filter $mainTaxonFilter = null;

    private Pagerfanta $pager;

    public function __construct(
        private int $maxItems,
        private int $page,
        ?ElasticaResultSet $resultSet = null,
        ?TaxonInterface $taxon = null,
    ) {
        // Empty result set
        if (null === $resultSet) {
            $this->totalHits = 0;
            $this->results = [];
            $this->filters = [];
        } else {
            /** @var Result $result */
            foreach ($resultSet as $result) {
                $this->results[] = $result->getModel();
            }
            $this->totalHits = $resultSet->getTotalHits();
            $this->initFilters($resultSet, $taxon);
        }

        $this->initPager();
    }

    private function initPager(): void
    {
        $adapter = new ResultSetAdapter($this);
        $this->pager = new Pagerfanta($adapter);
        $this->pager->setMaxPerPage($this->maxItems);
        $this->pager->setCurrentPage($this->page);
    }

    private function initFilters(ElasticaResultSet $resultSet, ?TaxonInterface $taxon = null): void
    {
        /** @var array{attributes: array, filters: array, taxons: array, mainTaxon: array, price: array} $aggregations */
        $aggregations = $resultSet->getAggregations();

        // Retrieve filters labels in aggregations
        /** @var array<string, string> $attributeNamesIndexedByCode */
        $attributeNamesIndexedByCode = [];

        /** @var array{doc_count: int, codes: array} $attributeAggregations */
        $attributeAggregations = $aggregations['attributes'];
        unset($attributeAggregations['doc_count']);

        /** @var array{doc_count_error_upper_bound: int, sum_other_doc_count: int, buckets: array<array-key, array>} $attributeCodes */
        $attributeCodes = $attributeAggregations['codes'];
        /** @var array{key: string, doc_count: int, names: array} $attributeCodeBucket */
        foreach ($attributeCodes['buckets'] as $attributeCodeBucket) {
            $attributeCode = $attributeCodeBucket['key'];
            /** @var array{doc_count_error_upper_bound: int, sum_other_doc_count: int, buckets: array<array-key, array>} $attributeNameTranslations */
            $attributeNameTranslations = $attributeCodeBucket['names'];
            /** @var array{key: string, doc_count: int} $attributeNameBucket */
            foreach ($attributeNameTranslations['buckets'] as $attributeNameBucket) {
                $attributeTranslationValue = $attributeNameBucket['key'];
                $attributeNamesIndexedByCode[$attributeCode] = $attributeTranslationValue;
                break;
            }
        }

        // Retrieve filters values in aggregations
        /** @var array{doc_count: int} $filterAggregationsWithCount */
        $filterAggregationsWithCount = $aggregations['filters'];
        unset($filterAggregationsWithCount['doc_count']);
        /** @var array<string, array{doc_count: int, values: array}> $filterAggregations */
        $filterAggregations = $filterAggregationsWithCount;
        foreach ($filterAggregations as $attributeCode => $aggregation) {
            $resultsCount = $aggregation['doc_count'];
            if (0 === $resultsCount) {
                continue;
            }
            $filter = new Filter(
                $attributeCode,
                $attributeNamesIndexedByCode[$attributeCode] ?? $attributeCode,
                $resultsCount,
            );
            /** @var array{doc_count_error_upper_bound: int, sum_other_doc_count: int, buckets: array<array-key, array>} $buckets */
            $filterAttributeValues = $aggregation['values'];
            /** @var array{key: string, doc_count: int} $bucket */
            foreach ($filterAttributeValues['buckets'] as $bucket) {
                $filter->addValue($bucket['key'], $bucket['doc_count']);
            }
            $this->filters[] = $filter;
        }
        $this->sortFilters();

        /** @var array{doc_count: int, codes: array} $taxonsAggregation */
        $taxonsAggregation = $aggregations['taxons'];
        $this->addTaxonFilter($taxonsAggregation, $taxon);

        /** @var array{doc_count: int, codes: array} $mainTaxonAggregation */
        $mainTaxonAggregation = $aggregations['mainTaxon'];
        $this->addMainTaxonFilter($mainTaxonAggregation, $taxon);

        /** @var array{doc_count: int, values: array{count: int, min: float, max: float, avg: float, sum: float}} $priceAggregation */
        $priceAggregation = $aggregations['price'];
        $this->addPriceFilter($priceAggregation);
    }

    /**
     * @return ResultInterface[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return Filter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getTotalHits(): int
    {
        return $this->totalHits;
    }

    public function getPager(): Pagerfanta
    {
        return $this->pager;
    }

    public function getTaxonFilter(): ?Filter
    {
        return $this->taxonFilter;
    }

    public function getMainTaxonFilter(): ?Filter
    {
        return $this->mainTaxonFilter;
    }

    public function getPriceFilter(): ?RangeFilter
    {
        return $this->priceFilter;
    }

    protected function sortFilters(): void
    {
        usort($this->filters, static function(Filter $filter1, Filter $filter2): int {
            // If same count we display the filters with more values before
            if ($filter1->getCount() === $filter2->getCount()) {
                return count($filter2->getValues()) > count($filter1->getValues()) ? 1 : -1;
            }

            return $filter2->getCount() > $filter1->getCount() ? 1 : -1;
        });
    }

    /**
     * @param array{doc_count: int, codes: array} $taxonAggregation
     */
    protected function addTaxonFilter(array $taxonAggregation, ?TaxonInterface $taxon): void
    {
        if ($taxonAggregation['doc_count'] <= 0) {
            return;
        }
        // Get current taxon level to retrieve only greater levels, in search we will take only the first level
        $currentTaxonLevel = $taxon ? $taxon->getLevel() : 0;

        // Get children taxon if we have current taxon
        $childrenTaxon = [];
        if ($taxon) {
            foreach ($taxon->getChildren() as $child) {
                $childrenTaxon[$child->getCode()] = $child->getLevel();
            }
        }

        $filter = new Filter(
            'taxon',
            'monsieurbiz_searchplugin.filters.taxon_filter',
            $taxonAggregation['doc_count'],
        );

        // Get taxon code in aggregation
        /** @var array{doc_count_error_upper_bound: int, sum_other_doc_count: int, buckets: array<array-key, array>} $taxonCodes */
        $taxonCodes = $taxonAggregation['codes'];
        /** @var array{key: string, doc_count: int, levels: array} $taxonCodeBucket */
        foreach ($taxonCodes['buckets'] as $taxonCodeBucket) {
            if (0 === $taxonCodeBucket['doc_count']) {
                continue;
            }
            $taxonCode = $taxonCodeBucket['key'];

            // Get taxon level in aggregation
            /** @var array{doc_count_error_upper_bound: int, sum_other_doc_count: int, buckets: array<array-key, array>} $taxonLevels */
            $taxonLevels = $taxonCodeBucket['levels'];
            /** @var array{key: int, doc_count: int, names: array} $taxonLevelBucket */
            foreach ($taxonLevels['buckets'] as $taxonLevelBucket) {
                $level = $taxonLevelBucket['key'];
                if ($level === ($currentTaxonLevel + 1) && (!$taxon || isset($childrenTaxon[$taxonCode]))) {
                    // Get taxon name in aggregation
                    /** @var array{doc_count_error_upper_bound: int, sum_other_doc_count: int, buckets: array<array-key, array>} $taxonNames */
                    $taxonNames = $taxonLevelBucket['names'];
                    /** @var array{key: string, doc_count: int} $taxonNameBucket */
                    foreach ($taxonNames['buckets'] as $taxonNameBucket) {
                        $filter->addValue($taxonNameBucket['key'], $taxonCodeBucket['doc_count']);
                        break 2;
                    }
                }
            }
        }

        // Put taxon filter in first if contains value
        if (count($filter->getValues())) {
            $this->taxonFilter = $filter;
        }
    }

    /**
     * @param array{doc_count: int, codes: array} $taxonAggregation
     */
    protected function addMainTaxonFilter(array $taxonAggregation, ?TaxonInterface $taxon): void
    {
        if ($taxonAggregation['doc_count'] <= 0) {
            return;
        }
        $filter = new Filter(
            'main_taxon',
            'monsieurbiz_searchplugin.filters.taxon_filter',
            $taxonAggregation['doc_count'],
        );

        // Get main taxon code in aggregation
        /** @var array{doc_count_error_upper_bound: int, sum_other_doc_count: int, buckets: array<array-key, array>} $taxonCodes */
        $taxonCodes = $taxonAggregation['codes'];
        /** @var array{key: string, doc_count: int, levels: array} $taxonCodeBucket */
        foreach ($taxonCodes['buckets'] as $taxonCodeBucket) {
            if (0 === $taxonCodeBucket['doc_count']) {
                continue;
            }
            // Get main taxon level in aggregation
            /** @var array{doc_count_error_upper_bound: int, sum_other_doc_count: int, buckets: array<array-key, array>} $levels */
            $levels = $taxonCodeBucket['levels'];
            /** @var array{key: int, doc_count: int, names: array} $taxonLevelBucket */
            foreach ($levels['buckets'] as $taxonLevelBucket) {
                // Get main taxon name in aggregation
                /** @var array{doc_count_error_upper_bound: int, sum_other_doc_count: int, buckets: array<array-key, array>} $names */
                $names = $taxonLevelBucket['names'];
                /** @var array{key: string, doc_count: int} $taxonNameBucket */
                foreach ($names['buckets'] as $taxonNameBucket) {
                    $taxonName = $taxonNameBucket['key'];
                    $filter->addValue($taxonName, $taxonCodeBucket['doc_count']);
                    break 2;
                }
            }
        }

        // Put taxon filter in first if contains value
        if (count($filter->getValues())) {
            $this->mainTaxonFilter = $filter;
        }
    }

    /**
     * @param array{doc_count: int, values: array{count: int, min: float, max: float, avg: float, sum: float}} $priceAggregation
     */
    protected function addPriceFilter(array $priceAggregation): void
    {
        if ($priceAggregation['doc_count'] <= 0) {
            return;
        }
        $this->priceFilter = new RangeFilter(
            'price',
            'monsieurbiz_searchplugin.filters.price_filter',
            'monsieurbiz_searchplugin.filters.price_min',
            'monsieurbiz_searchplugin.filters.price_max',
            (int) floor(($priceAggregation['values']['min'] ?? 0) / 100),
            (int) ceil(($priceAggregation['values']['max'] ?? 0) / 100)
        );
    }
}
