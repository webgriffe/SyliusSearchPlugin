<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Provider;

use MonsieurBiz\SyliusSearchPlugin\Exception\MissingConfigFileException;
use MonsieurBiz\SyliusSearchPlugin\Exception\ReadFileException;
use MonsieurBiz\SyliusSearchPlugin\Model\Config\FilesConfig;

class SearchQueryProvider
{
    private FilesConfig $filesConfig;

    /**
     * @throws MissingConfigFileException
     */
    public function __construct(array $files)
    {
        $this->filesConfig = new FilesConfig($files);
    }

    /**
     * @throws ReadFileException
     */
    public function getSearchQuery(): string
    {
        return $this->getQuery($this->filesConfig->getSearchPath());
    }

    /**
     * @throws ReadFileException
     */
    public function getInstantQuery(): ?string
    {
        return $this->getQuery($this->filesConfig->getInstantPath());
    }

    /**
     * @throws ReadFileException
     */
    public function getTaxonQuery(): ?string
    {
        return $this->getQuery($this->filesConfig->getTaxonPath());
    }

    /**
     * @throws ReadFileException
     */
    private function getQuery(string $path): string
    {
        $query = @file_get_contents($path);
        if (false === $query) {
            throw new ReadFileException(sprintf('Error while opening file "%s".', $path));
        }

        return $query;
    }
}
