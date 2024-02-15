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

/**
 * @psalm-suppress DeprecatedInterface
 */
class Result extends Document implements ResultInterface, ResultWithPromotionsInterface
{
    public function getUniqId(): string
    {
        if (!$this->getType()) {
            throw new MissingParamException('Missing "type" for document');
        }
        if (!$this->getId()) {
            throw new MissingParamException('Missing "ID" for document');
        }

        return sprintf('%s-%d', $this->getType(), $this->getId());
    }

    public function getAttribute(string $code): ?Attributes
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getCode() === $code) {
                return $attribute;
            }
        }

        return null;
    }

    public function getPriceByChannelAndCurrency(string $channelCode, string $currencyCode): ?Price
    {
        if (null === $this->getPrice()) {
            return null;
        }
        foreach ($this->getPrice() as $price) {
            if ($price->getChannel() === $channelCode && $price->getCurrency() === $currencyCode) {
                return $price;
            }
        }

        throw new MissingPriceException(sprintf(
            'Price not found for channel "%s" and currency "%s"',
            $channelCode,
            $currencyCode,
        ));
    }

    public function getOriginalPriceByChannelAndCurrency(string $channelCode, string $currencyCode): ?Price
    {
        if (null === $this->getOriginalPrice()) {
            return null;
        }

        foreach ($this->getOriginalPrice() as $price) {
            if ($price->getChannel() === $channelCode && $price->getCurrency() === $currencyCode) {
                return $price;
            }
        }

        return null;
    }

    public function getLocale(): string
    {
        foreach ($this->getAttributes() as $attribute) {
            if ($attribute->getLocale()) {
                return $attribute->getLocale();
            }
        }

        throw new MissingLocaleException('Locale not found in document');
    }

    public function getUrlParams(): UrlParamsProvider
    {
        if ($this->getType() === 'product') {
            return new UrlParamsProvider(
                'sylius_shop_product_show',
                ['slug' => $this->getSlug(), '_locale' => $this->getLocale()],
            );
        }

        throw new NotSupportedTypeException(sprintf(
            'Object type "%s" not supported to get URL',
            $this->getType(),
        ));
    }

    public function addChannel(string $channel): ResultInterface
    {
        $channelCodes = $this->getChannel() ? array_unique(array_merge($this->getChannel(), [$channel])) : [$channel];
        $this->setChannel($channelCodes);

        return $this;
    }

    public function addTaxon(
        string $code,
        string $name,
        int $position,
        int $level,
        int $productPosition,
    ): ResultInterface {
        $taxon = new Taxon();
        $taxon->setCode($code)->setPosition($position)->setName($name)->setLevel($level)->setProductPosition($productPosition);
        $this->setTaxon($this->getTaxon() ? array_merge($this->getTaxon(), [$taxon]) : [$taxon]);

        return $this;
    }

    public function addPrice(string $channel, string $currency, int $value): ResultInterface
    {
        $this->addPriceWithPromotions($channel, $currency, $value, []);

        return $this;
    }

    public function addPriceWithPromotions(string $channel, string $currency, int $value, array $appliedPromotions = []): ResultInterface
    {
        $price = new Price();
        $price
            ->setChannel($channel)
            ->setCurrency($currency)
            ->setValue($value)
            ->setAppliedPromotions($appliedPromotions)
        ;
        $this->setPrice($this->getPrice() ? array_merge($this->getPrice(), [$price]) : [$price]);

        return $this;
    }

    public function addOriginalPrice(string $channel, string $currency, int $value): ResultInterface
    {
        $price = new Price();
        $price->setChannel($channel)->setCurrency($currency)->setValue($value);
        $this->setOriginalPrice($this->getOriginalPrice() ? array_merge($this->getOriginalPrice(), [$price]) : [$price]);

        return $this;
    }

    public function addAttribute(string $code, string $name, array $value, string $locale, int $score): ResultInterface
    {
        $attribute = new Attributes();
        $attribute->setCode($code)->setName($name)->setValue($value)->setLocale($locale)->setScore($score);
        $this->setAttributes($this->getAttributes() ? array_merge($this->getAttributes(), [$attribute]) : [$attribute]);

        return $this;
    }
}
