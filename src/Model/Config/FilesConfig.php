<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Config;

use MonsieurBiz\SyliusSearchPlugin\Exception\MissingConfigFileException;

class FilesConfig
{
    private string $searchPath;

    private string $instantPath;

    private string $taxonPath;

    /**
     * @throws MissingConfigFileException
     */
    public function __construct(array $files)
    {
        if (!isset($files['search'], $files['instant'], $files['taxon'])) {
            throw new MissingConfigFileException('You need to have 3 config files : search, instant and taxon');
        }
        $this->searchPath = $files['search'];
        $this->instantPath = $files['instant'];
        $this->taxonPath = $files['taxon'];
    }

    public function getSearchPath(): string
    {
        return $this->searchPath;
    }

    public function getInstantPath(): string
    {
        return $this->instantPath;
    }

    public function getTaxonPath(): string
    {
        return $this->taxonPath;
    }
}
