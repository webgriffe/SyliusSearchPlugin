<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Document\Index;

use JoliCode\Elastically\Client;
use JoliCode\Elastically\IndexBuilder;
use JoliCode\Elastically\Indexer;
use MonsieurBiz\SyliusSearchPlugin\Provider\DocumentRepositoryProvider;

abstract class AbstractIndex
{
    public const DOCUMENT_INDEX_NAME = 'documents';

    protected DocumentRepositoryProvider $documentRepositoryProvider;

    public function __construct(
        private Client $client,
    ) {
    }

    protected function getClient(): Client
    {
        return $this->client;
    }

    protected function getIndexName(string $locale): string
    {
        return self::DOCUMENT_INDEX_NAME . '-' . strtolower($locale);
    }

    protected function getIndexBuilder(): IndexBuilder
    {
        return $this->client->getIndexBuilder();
    }

    protected function getIndexer(): Indexer
    {
        return $this->client->getIndexer();
    }
}
