<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Helper;

use MonsieurBiz\SyliusSearchPlugin\Exception\MissingLocaleException;
use MonsieurBiz\SyliusSearchPlugin\Exception\NotSupportedTypeException;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\Result;
use MonsieurBiz\SyliusSearchPlugin\Provider\UrlParamsProvider;

class RenderDocumentUrlHelper
{
    /**
     * @param Result $document
     *
     * @throws MissingLocaleException
     * @throws NotSupportedTypeException
     *
     * @return UrlParamsProvider
     */
    public function getUrlParams(Result $document): UrlParamsProvider
    {
        if ($document->getType() === 'product') {
            return new UrlParamsProvider('sylius_shop_product_show', ['slug' => $document->getSlug(), '_locale' => $document->getLocale()]);
        }

        throw new NotSupportedTypeException(sprintf('Object type "%s" not supported to get URL', $document->getType()));
    }
}
