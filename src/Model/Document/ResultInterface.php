<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Document;

use MonsieurBiz\SyliusSearchPlugin\Exception\MissingLocaleException;
use MonsieurBiz\SyliusSearchPlugin\Exception\MissingParamException;
use MonsieurBiz\SyliusSearchPlugin\Exception\MissingPriceException;
use MonsieurBiz\SyliusSearchPlugin\Exception\NotSupportedTypeException;
use MonsieurBiz\SyliusSearchPlugin\generated\Model\Attributes;
use MonsieurBiz\SyliusSearchPlugin\generated\Model\Document;
use MonsieurBiz\SyliusSearchPlugin\generated\Model\Price;
use MonsieurBiz\SyliusSearchPlugin\generated\Model\Taxon;
use MonsieurBiz\SyliusSearchPlugin\Provider\UrlParamsProvider;

interface ResultInterface
{
    /**
     * @throws MissingParamException
     */
    public function getUniqId(): string;

    public function getAttribute(string $code): ?Attributes;

    /**
     * @throws MissingPriceException
     */
    public function getPriceByChannelAndCurrency(string $channelCode, string $currencyCode): ?Price;

    public function getOriginalPriceByChannelAndCurrency(string $channelCode, string $currencyCode): ?Price;

    /**
     * @throws MissingLocaleException
     */
    public function getLocale(): string;

    /**
     * @throws MissingLocaleException
     * @throws NotSupportedTypeException
     */
    public function getUrlParams(): UrlParamsProvider;

    public function addChannel(string $channel): self;

    public function addTaxon(
        string $code,
        string $name,
        int $position,
        int $level,
        int $productPosition,
    ): self;

    /**
     * @deprecated Will be replaced by method addPriceWithPromotions in ResultWithPromotionsInterface
     */
    public function addPrice(string $channel, string $currency, int $value): self;

    public function addOriginalPrice(string $channel, string $currency, int $value): self;

    public function addAttribute(string $code, string $name, array $value, string $locale, int $score): self;
}
