<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Document\Index;

use Elastica\Document;
use Elastica\Exception\ExceptionInterface;
use Elastica\Exception\ResponseException;
use JoliCode\Elastically\Client;
use MonsieurBiz\SyliusSearchPlugin\Exception\MissingParamException;
use MonsieurBiz\SyliusSearchPlugin\Exception\ReadOnlyIndexException;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\ResultInterface;
use MonsieurBiz\SyliusSearchPlugin\Model\Documentable\DocumentableInterface;
use MonsieurBiz\SyliusSearchPlugin\Provider\DocumentRepositoryProvider;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

class Indexer extends AbstractIndex
{
    /** @var string[] */
    private array $locales = [];

    /**
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     */
    public function __construct(
        Client $client,
        DocumentRepositoryProvider $documentRepositoryProvider,
        private RepositoryInterface $localeRepository
    ) {
        parent::__construct($client);
        $this->documentRepositoryProvider = $documentRepositoryProvider;
    }

    /**
     * @return string[]
     */
    public function getLocales(): array
    {
        if ($this->locales === []) {
            $locales = $this->localeRepository->findAll();
            $this->locales = array_map(
                static function(LocaleInterface $locale) {
                    return (string) $locale->getCode();
                },
                $locales
            );
        }

        return $this->locales;
    }

    /**
     * @throws ReadOnlyIndexException
     * @throws MissingParamException
     * @throws ExceptionInterface
     */
    public function indexAll(): void
    {
        foreach ($this->getLocales() as $locale) {
            $this->indexAllByLocale($locale);
        }
    }

    /**
     * @throws ExceptionInterface
     * @throws ReadOnlyIndexException
     * @throws MissingParamException
     */
    public function indexAllByLocale(string $locale): void
    {
        $indexName = $this->getIndexName($locale);
        $newIndex = $this->getIndexBuilder()->createIndex($indexName);

        $repositories = $this->documentRepositoryProvider->getRepositories();
        foreach ($repositories as $repository) {
            // @TODO this could be improved by introducing a DocumentableRepositoryInterface
            if ($repository instanceof ProductRepositoryInterface) {
                $documents = $repository->findBy(['enabled' => true]);
            } else {
                $documents = $repository->findAll();
            }
            /** @var DocumentableInterface|mixed $document */
            foreach ($documents as $document) {
                Assert::isInstanceOf($document, DocumentableInterface::class);
                $convertToDocument = $document->convertToDocument($locale);
                $this->getIndexer()->scheduleIndex(
                    $newIndex,
                    new Document($convertToDocument->getUniqId(), $convertToDocument),
                );
            }
        }

        $this->getIndexBuilder()->markAsLive(
            $newIndex,
            $indexName
        );

        $this->getIndexer()->flush();

        $this->getIndexer()->refresh($indexName);

        try {
            $this->getIndexBuilder()->purgeOldIndices($indexName);
        } catch (ResponseException $exception) {
            throw new ReadOnlyIndexException($exception->getMessage());
        }
    }

    /**
     * @throws ExceptionInterface
     * @throws MissingParamException
     */
    public function indexOne(DocumentableInterface $subject): void
    {
        foreach ($this->getLocales() as $locale) {
            $this->indexOneByLocale(
                $subject->convertToDocument($locale),
                $locale,
            );

            $this->getIndexer()->flush();
        }
    }

    /**
     * @throws MissingParamException
     * @throws ExceptionInterface
     */
    public function indexOneByLocale(ResultInterface $result, string $locale): void
    {
        /** @psalm-suppress InvalidArgument */
        $document = new Document($result->getUniqId(), $result);

        $this->getIndexer()->scheduleIndex(
            $this->getClient()->getIndex($this->getIndexName($locale)),
            $document,
        );
    }

    /**
     * @throws ExceptionInterface
     * @throws MissingParamException
     */
    public function removeOne(DocumentableInterface $subject): void
    {
        foreach ($this->getLocales() as $locale) {
            $this->removeOneByLocale($subject->convertToDocument($locale), $locale);
            $this->getIndexer()->flush();
        }
    }

    /**
     * @throws ExceptionInterface
     * @throws MissingParamException
     */
    public function removeOneByLocale(ResultInterface $document, string $locale): void
    {
        $this->getIndexer()->scheduleDelete(
            $this->getClient()->getIndex($this->getIndexName($locale)),
            $document->getUniqId()
        );
    }
}
