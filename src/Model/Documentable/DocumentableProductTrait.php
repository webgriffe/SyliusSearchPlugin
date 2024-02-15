<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Documentable;

use Doctrine\Common\Collections\Collection;
use MonsieurBiz\SyliusSearchPlugin\generated\Model\Taxon as DocumentTaxon;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\Result;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\ResultInterface;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\ResultWithPromotionsInterface;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\Channel;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\Image;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

trait DocumentableProductTrait
{
    /** @return Collection<array-key, ProductVariantInterface> */
    abstract public function getEnabledVariants(): Collection;

    public function getDocumentType(): string
    {
        return 'product';
    }

    public function createResult(): ResultInterface
    {
        return new Result();
    }

    public function convertToDocument(string $locale): ResultInterface
    {
        $document = $this->createResult();

        // Document data
        $document->setType($this->getDocumentType());
        $document->setCode($this->getCode());
        $document->setId($this->getId());
        $document->setEnabled($this->isEnabled());
        $document->setInStock($this->getProductHasVariantInStock());
        $document->setSlug($this->getTranslation($locale)->getSlug());

        $document = $this->addImagesInDocument($document);
        $document = $this->addChannelsInDocument($document);
        $document = $this->addPricesInDocument($document);
        $document = $this->addTaxonsInDocument($document, $locale);

        $document->addAttribute('name', 'Name', [$this->getTranslation($locale)->getName()], $locale, 50);
        $document->addAttribute('description', 'Description', [$this->getTranslation($locale)->getDescription()], $locale, 10);
        $document->addAttribute('short_description', 'Short description', [$this->getTranslation($locale)->getShortDescription()], $locale, 10);
        $document->addAttribute('created_at', 'Creation Date', [$this->getCreatedAt()], $locale, 1);

        $document = $this->addAttributesInDocument($document, $locale);

        return $this->addOptionsInDocument($document, $locale);
    }

    protected function addImagesInDocument(ResultInterface $document): ResultInterface
    {
        /** @var Image $image */
        if ($image = $this->getImages()->first()) {
            $document->setImage($image->getPath());
        }

        return $document;
    }

    protected function addChannelsInDocument(ResultInterface $document): ResultInterface
    {
        /** @var Channel $channel */
        foreach ($this->getChannels() as $channel) {
            $document->addChannel($channel->getCode());
        }

        return $document;
    }

    protected function addPricesInDocument(ResultInterface $document): ResultInterface
    {
        /** @var Channel $channel */
        foreach ($this->getChannels() as $channel) {
            $cheapestVariantChannelPricing = $this->getCheapestVariantChannelPricingForChannel($channel);
            if ($cheapestVariantChannelPricing !== null) {
                /** @var CurrencyInterface $currency */
                foreach ($channel->getCurrencies() as $currency) {
                    $variantPrice = $cheapestVariantChannelPricing->getPrice();
                    if ($variantPrice < $cheapestVariantChannelPricing->getMinimumPrice()) {
                        $variantPrice = $cheapestVariantChannelPricing->getMinimumPrice();
                    }
                    $appliedPromotions = [];
                    /** @var CatalogPromotionInterface $appliedPromotion */
                    foreach ($cheapestVariantChannelPricing->getAppliedPromotions() as $appliedPromotion) {
                        $label = $appliedPromotion->getLabel();
                        if ($label === null) {
                            continue;
                        }
                        $appliedPromotions[] = $label;
                    }
                    if ($document instanceof ResultWithPromotionsInterface) {
                        $document->addPriceWithPromotions($channel->getCode(), $currency->getCode(), $variantPrice, $appliedPromotions);
                    } else {
                        trigger_deprecation(sprintf('Your result class should implement also the %s interface, the addPrice method will be removed in next major release.', ResultWithPromotionsInterface::class));
                        $document->addPrice($channel->getCode(), $currency->getCode(), $variantPrice);
                    }
                    $originalPrice = $cheapestVariantChannelPricing->getOriginalPrice();
                    if ($originalPrice !== null) {
                        $document->addOriginalPrice($channel->getCode(), $currency->getCode(), $originalPrice);
                    }
                }
            }
        }

        return $document;
    }

    protected function addTaxonsInDocument(ResultInterface $document, string $locale): ResultInterface
    {
        /** @var TaxonInterface $mainTaxon */
        if ($mainTaxon = $this->getMainTaxon()) {
            $taxon = new DocumentTaxon();
            $taxon
                ->setName($mainTaxon->getTranslation($locale)->getName())
                ->setCode($mainTaxon->getCode())
                ->setPosition($mainTaxon->getPosition())
                ->setLevel($mainTaxon->getLevel())
            ;
            $document->setMainTaxon($taxon);
        }

        /** @var ProductTaxonInterface $productTaxon */
        foreach ($this->getProductTaxons() as $productTaxon) {
            $document->addTaxon(
                $productTaxon->getTaxon()->getCode(),
                $productTaxon->getTaxon()->getTranslation($locale)->getName(),
                $productTaxon->getTaxon()->getPosition(),
                $productTaxon->getTaxon()->getLevel(),
                $productTaxon->getPosition()
            );
        }

        return $document;
    }

    protected function addAttributesInDocument(ResultInterface $document, string $locale): ResultInterface
    {
        /** @var AttributeValueInterface $attributeValue */
        foreach ($this->getAttributesByLocale($locale, $locale) as $attributeValue) {
            $productAttributeValues = [];
            $attribute = $attributeValue->getAttribute();
            if ($attribute === null) {
                continue;
            }
            if ($attribute->getType() === SelectAttributeType::TYPE) {
                // Add all the selected values in the current locale if it exists, otherwise use the current value
                foreach ($attributeValue->getValue() as $value) {
                    if (isset($attribute->getConfiguration()['choices'][$value][$locale])) {
                        $productAttributeValues[] = $attribute->getConfiguration()['choices'][$value][$locale];

                        continue;
                    }
                    $productAttributeValues[] = $value;
                }
            } else {
                $productAttributeValues[] = $attributeValue->getValue();
            }
            $document->addAttribute($attributeValue->getCode(), $attributeValue->getName(), $productAttributeValues, $attributeValue->getLocaleCode() ?? $locale, 1);
        }

        return $document;
    }

    protected function addOptionsInDocument(ResultInterface $document, string $locale): ResultInterface
    {
        $options = [];
        foreach ($this->getEnabledVariants() as $variant) {
            foreach ($variant->getOptionValues() as $val) {
                if (!isset($options[$val->getOption()->getCode()])) {
                    $options[$val->getOption()->getCode()] = [
                        'name' => $val->getOption()->getTranslation($locale)->getName(),
                        'values' => [],
                    ];
                }
                $options[$val->getOption()->getCode()]['values'][$val->getCode()] = $val->getTranslation($locale)->getValue();
            }
        }

        foreach ($options as $optionCode => $option) {
            $document->addAttribute($optionCode, $option['name'], array_values($option['values']), $locale, 1);
        }

        return $document;
    }

    private function getCheapestVariantChannelPricingForChannel(ChannelInterface $channel): ?ChannelPricingInterface
    {
        $cheapestVariantChannelPricing = null;
        $cheapestProductPrice = null;
        $variants = $this->getEnabledVariants();
        foreach ($variants as $variant) {
            $channelPrice = $variant->getChannelPricingForChannel($channel);
            if ($channelPrice === null) {
                continue;
            }
            $variantPrice = $channelPrice->getPrice();
            if ($variantPrice < $channelPrice->getMinimumPrice()) {
                $variantPrice = $channelPrice->getMinimumPrice();
            }
            if (null === $cheapestProductPrice || $variantPrice < $cheapestProductPrice) {
                $cheapestProductPrice = $variantPrice;
                $cheapestVariantChannelPricing = $channelPrice;
            }
        }

        return $cheapestVariantChannelPricing;
    }

    private function getProductHasVariantInStock(): bool
    {
        $variants = $this->getEnabledVariants();
        /** @var ProductVariantInterface $variant */
        foreach ($variants as $variant) {
            if (!$variant->isTracked() || 1 <= ($variant->getOnHand() - $variant->getOnHold())) {
                return true;
            }
        }
        return false;
    }
}
