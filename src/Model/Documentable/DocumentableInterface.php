<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Documentable;

use MonsieurBiz\SyliusSearchPlugin\Model\Document\ResultInterface;

interface DocumentableInterface
{
    public function getDocumentType(): string;

    public function convertToDocument(string $locale): ResultInterface;
}
