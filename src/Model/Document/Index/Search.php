<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Document\Index;

use Elastica\Exception\Connection\HttpException;
use Elastica\Exception\ResponseException;
use JoliCode\Elastically\Client;
use MonsieurBiz\SyliusSearchPlugin\Exception\ReadFileException;
use MonsieurBiz\SyliusSearchPlugin\Helper\AggregationHelper;
use MonsieurBiz\SyliusSearchPlugin\Helper\FilterHelper;
use MonsieurBiz\SyliusSearchPlugin\Helper\SortHelper;
use MonsieurBiz\SyliusSearchPlugin\Model\ArrayObject;
use MonsieurBiz\SyliusSearchPlugin\Model\Config\GridConfig;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\ResultSet;
use MonsieurBiz\SyliusSearchPlugin\Provider\SearchQueryProvider;
use Psr\Log\LoggerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

class Search extends AbstractIndex
{
    public function __construct(
        Client $client,
        private SearchQueryProvider $searchQueryProvider,
        private ChannelContextInterface $channelContext,
        private LoggerInterface $logger,
    ) {
        parent::__construct($client);
    }

    public function search(GridConfig $gridConfig): ResultSet
    {
        try {
            return $this->query($gridConfig, $this->getSearchQuery($gridConfig));
        } catch (ReadFileException $exception) {
            $this->logger->critical($exception->getMessage());

            return new ResultSet($gridConfig->getLimit(), $gridConfig->getPage());
        }
    }

    public function instant(GridConfig $gridConfig): ResultSet
    {
        try {
            return $this->query($gridConfig, $this->getInstantQuery($gridConfig));
        } catch (ReadFileException $exception) {
            $this->logger->critical($exception->getMessage());

            return new ResultSet($gridConfig->getLimit(), $gridConfig->getPage());
        }
    }

    public function taxon(GridConfig $gridConfig): ResultSet
    {
        try {
            return $this->query($gridConfig, $this->getTaxonQuery($gridConfig));
        } catch (ReadFileException $exception) {
            $this->logger->critical($exception->getMessage());

            return new ResultSet($gridConfig->getLimit(), $gridConfig->getPage());
        }
    }

    private function query(GridConfig $gridConfig, array $query): ResultSet
    {
        try {
            /** @psalm-suppress InvalidArgument */
            $results = $this->getClient()->getIndex($this->getIndexName($gridConfig->getLocale()))->search(
                $query,
                $gridConfig->getLimit(),
            );
        } catch (HttpException|ResponseException $exception) {
            $this->logger->critical($exception->getMessage());

            return new ResultSet($gridConfig->getLimit(), $gridConfig->getPage());
        }

        return new ResultSet($gridConfig->getLimit(), $gridConfig->getPage(), $results, $gridConfig->getTaxon());
    }

    /**
     * @throws ReadFileException
     */
    private function getSearchQuery(GridConfig $gridConfig): array
    {
        $query = $this->searchQueryProvider->getSearchQuery();

        // Replace params
        $query = str_replace(
            ['{{QUERY}}', '{{CHANNEL}}'],
            [$gridConfig->getQuery(), (string) $this->channelContext->getChannel()->getCode()],
            $query,
        );

        // Convert query to array
        $query = $this->parseQuery($query);

        $appliedFilters = FilterHelper::buildFilters($gridConfig->getAppliedFilters());
        if (isset($appliedFilters['bool']['filter'])) {
            // Will retrieve filters after we applied the current ones
            $query['query']['bool']['filter'] = array_merge(
                $query['query']['bool']['filter'],
                $appliedFilters['bool']['filter'],
            );
        } elseif ($appliedFilters !== []) {
            // Will retrieve filters before we applied the current ones
            $query['post_filter'] = new ArrayObject($appliedFilters); // Use custom ArrayObject because Elastica make `toArray` on it.
        }

        // Manage limits
        $from = ($gridConfig->getPage() - 1) * $gridConfig->getLimit();
        $query['from'] = max(0, $from);
        $query['size'] = max(1, $gridConfig->getLimit());

        // Manage sorting
        $channelCode = $this->channelContext->getChannel()->getCode();
        foreach ($gridConfig->getSorting() as $field => $order) {
            $query['sort'][] = SortHelper::getSortParamByField($field, $channelCode, $order);

            break; // only 1
        }

        // Manage filters
        $aggs = AggregationHelper::buildAggregations($gridConfig->getFilters());
        if ($aggs !== []) {
            $query['aggs'] = AggregationHelper::buildAggregations($gridConfig->getFilters());
        }

        return $query;
    }

    private function getInstantQuery(GridConfig $gridConfig): array
    {
        $query = $this->searchQueryProvider->getInstantQuery();
        Assert::notNull($query);

        // Replace params
        $query = str_replace(
            ['{{QUERY}}', '{{CHANNEL}}'],
            [$gridConfig->getQuery(), (string) $this->channelContext->getChannel()->getCode()],
            $query,
        );

        // Convert query to array
        return $this->parseQuery($query);
    }

    /**
     * @throws ReadFileException
     */
    private function getTaxonQuery(GridConfig $gridConfig): array
    {
        $query = $this->searchQueryProvider->getTaxonQuery();
        Assert::notNull($query);

        // Replace params
        $query = str_replace(
            ['{{TAXON}}', '{{CHANNEL}}'],
            [(string) $gridConfig->getTaxon()?->getCode(), (string) $this->channelContext->getChannel()->getCode()],
            $query,
        );

        // Convert query to array
        $query = $this->parseQuery($query);

        // Apply filters
        $appliedFilters = FilterHelper::buildFilters($gridConfig->getAppliedFilters());
        if (isset($appliedFilters['bool']['filter'])) {
            // Will retrieve filters after we applied the current ones
            $query['query']['bool']['filter'] = array_merge(
                $query['query']['bool']['filter'],
                $appliedFilters['bool']['filter'],
            );
        } elseif ($appliedFilters !== []) {
            // Will retrieve filters before we applied the current ones
            $query['post_filter'] = new ArrayObject($appliedFilters); // Use custom ArrayObject because Elastica make `toArray` on it.
        }

        // Manage limits
        $from = ($gridConfig->getPage() - 1) * $gridConfig->getLimit();
        $query['from'] = max(0, $from);
        $query['size'] = max(1, $gridConfig->getLimit());

        // Manage sorting
        $channelCode = $this->channelContext->getChannel()->getCode();
        foreach ($gridConfig->getSorting() as $field => $order) {
            $query['sort'][] = SortHelper::getSortParamByField($field, $channelCode, $order, $gridConfig->getTaxon()->getCode());

            break; // only 1
        }

        // Manage filters
        $aggs = AggregationHelper::buildAggregations($gridConfig->getFilters());
        if ($aggs !== []) {
            $query['aggs'] = AggregationHelper::buildAggregations($gridConfig->getFilters());
        }

        return $query;
    }

    /**
     * @return array{query: array{bool: array{filter: array, must: array, should: array}}}
     */
    private function parseQuery(string $query): array
    {
        /** @var array{query: array{bool: array{filter: array, must: array, should: array}}} $parsedYaml */
        $parsedYaml = Yaml::parse($query);

        return $parsedYaml;
    }
}
