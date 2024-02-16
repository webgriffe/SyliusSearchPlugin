<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Config;

use MonsieurBiz\SyliusSearchPlugin\Exception\UnknownGridConfigType;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use function in_array;

class GridConfig
{
    public const SEARCH_TYPE = 'search';
    public const TAXON_TYPE = 'taxon';
    public const INSTANT_TYPE = 'instant';

    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';

    public const FALLBACK_LIMIT = 10;

    private bool $isInitialized = false;

    private string $type;

    private string $locale;

    private string $query;

    private int $page;

    /** @var int[] */
    private array $limits;

    private int $limit;

    /** @var string[] */
    private array $sorting;

    private ?TaxonInterface $taxon = null;

    private array $appliedFilters;

    /** @var string[]|null */
    private ?array $filterableAttributes = null;

    /** @var string[]|null */
    private ?array $filterableOptions = null;

    /**
     * @param array $config
     * @param RepositoryInterface<ProductAttributeInterface> $productAttributeRepository
     * @param RepositoryInterface<ProductOptionInterface> $productOptionRepository
     */
    public function __construct(
        private array $config,
        private RepositoryInterface $productAttributeRepository,
        private RepositoryInterface $productOptionRepository
    ) {
    }

    public function init(string $type, Request $request, ?TaxonInterface $taxon = null): void
    {
        if ($this->isInitialized) {
            return;
        }

        switch ($type) {
            case self::SEARCH_TYPE:
                // Set type, locale, page and query
                $this->type = $type;
                $this->locale = $request->getLocale();
                $this->page = max(1, (int) $request->get('page'));
                $this->query = htmlspecialchars(urldecode($request->get('query')));

                // Set sorting
                $availableSorting = $this->config['sorting']['search'] ?? [];
                $this->sorting = $this->cleanSorting($request->get('sorting'), $availableSorting);

                // Set limit
                $this->limit = max(1, (int) $request->get('limit'));
                $this->limits = $this->config['limits']['search'] ?? [];
                if (!in_array($this->limit, $this->limits, true)) {
                    $this->limit = $this->config['default_limit']['search'] ?? self::FALLBACK_LIMIT;
                }

                // Set applied filters
                $this->appliedFilters = $request->get('attribute') ?? [];
                if (null !== $priceFilter = $request->get('price')) {
                    $this->appliedFilters['price'] = $priceFilter;
                }

                $this->isInitialized = true;
                break;
            case self::TAXON_TYPE:
                // Set type, locale, page and taxon
                $this->type = $type;
                $this->locale = $request->getLocale();
                $this->page = max(1, (int) $request->get('page'));
                $this->taxon = $taxon;

                // Set sorting
                $availableSorting = $this->config['sorting']['taxon'] ?? [];
                $this->sorting = $this->cleanSorting($request->get('sorting'), $availableSorting);
                if (count($this->sorting) === 0) {
                    $this->sorting['position'] = self::SORT_ASC;
                }

                // Set applied filters
                $this->appliedFilters = $request->get('attribute') ?? [];
                if (null !== $priceFilter = $request->get('price')) {
                    $this->appliedFilters['price'] = $priceFilter;
                }

                // Set limit
                $this->limit = max(1, (int) $request->get('limit'));
                $this->limits = $this->config['limits']['taxon'] ?? [];
                if (!in_array($this->limit, $this->limits, true)) {
                    $this->limit = $this->config['default_limit']['taxon'] ?? self::FALLBACK_LIMIT;
                }
                $this->isInitialized = true;
                break;
            case self::INSTANT_TYPE:
                // Set type, locale, page and query
                $this->type = $type;
                $this->locale = $request->getLocale();
                $this->page = 1;
                $this->query = htmlspecialchars(urldecode($request->get('query')));

                // Set limit
                $this->limit = $this->config['default_limit']['instant'] ?? self::FALLBACK_LIMIT;
                $this->isInitialized = true;
                break;
            default:
                throw new UnknownGridConfigType();
        }
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int[]
     */
    public function getLimits(): array
    {
        return $this->limits;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return string[]
     */
    public function getSorting(): array
    {
        return $this->sorting;
    }

    /**
     * @return string[]
     */
    public function getAttributeFilters(): array
    {
        if (null === $this->filterableAttributes) {
            $attributes = $this->productAttributeRepository->findBy([
                'filterable' => true,
            ]);
            $this->filterableAttributes = [];
            foreach ($attributes as $attribute) {
                $this->filterableAttributes[] = (string) $attribute->getCode();
            }
        }

        return $this->filterableAttributes;
    }

    /**
     * @return string[]
     */
    public function getOptionFilters(): array
    {
        if (null === $this->filterableOptions) {
            $options = $this->productOptionRepository->findBy([
                'filterable' => true,
            ]);
            $this->filterableOptions = [];
            foreach ($options as $option) {
                $this->filterableOptions[] = (string) $option->getCode();
            }
        }

        return $this->filterableOptions;
    }

    public function haveToApplyManuallyFilters(): bool
    {
        return $this->config['filters']['apply_manually'] ?? false;
    }

    public function useMainTaxonForFilter(): bool
    {
        return $this->config['filters']['use_main_taxon'] ?? false;
    }

    /**
     * @return string[]
     */
    public function getFilters(): array
    {
        return array_merge($this->getAttributeFilters(), $this->getOptionFilters());
    }

    public function getAppliedFilters(): array
    {
        return $this->appliedFilters;
    }

    public function getTaxon(): ?TaxonInterface
    {
        return $this->taxon;
    }

    private function cleanSorting(?array $sorting, array $availableSorting): array
    {
        if ($sorting === null) {
            return  [];
        }

        foreach ($sorting as $field => $order) {
            if (!in_array($field, $availableSorting, true) ||
                !in_array($order, [self::SORT_ASC, self::SORT_DESC], true)
            ) {
                unset($sorting[$field]);
            }
        }

        return $sorting;
    }
}
