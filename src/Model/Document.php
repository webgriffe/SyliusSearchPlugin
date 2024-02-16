<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model;

class Document
{
    protected ?string $type = null;

    protected ?string $code = null;

    protected ?int $id = null;

    protected ?bool $enabled = null;

    protected ?bool $inStock = null;

    protected ?string $slug = null;

    protected ?string $image = null;

    /** @var string[]|null */
    protected ?array $channel = null;

    protected ?Taxon $mainTaxon = null;

    /** @var Taxon[]|null */
    protected ?array $taxon = null;

    /** @var Attributes[]|null */
    protected ?array $attributes = null;

    /** @var Price[]|null */
    protected ?array $price = null;

    /** @var Price[]|null */
    protected ?array $originalPrice = null;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getInStock(): ?bool
    {
        return $this->inStock;
    }

    public function setInStock(?bool $inStock): self
    {
        $this->inStock = $inStock;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getChannel(): ?array
    {
        return $this->channel;
    }

    /**
     * @param string[]|null $channel
     */
    public function setChannel(?array $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getMainTaxon(): ?Taxon
    {
        return $this->mainTaxon;
    }

    /**
     * @param Taxon|null $mainTaxon
     */
    public function setMainTaxon(?Taxon $mainTaxon): self
    {
        $this->mainTaxon = $mainTaxon;

        return $this;
    }

    /**
     * @return Taxon[]|null
     */
    public function getTaxon(): ?array
    {
        return $this->taxon;
    }

    /**
     * @param Taxon[]|null $taxon
     */
    public function setTaxon(?array $taxon): self
    {
        $this->taxon = $taxon;

        return $this;
    }

    /**
     * @return Attributes[]|null
     */
    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    /**
     * @param Attributes[]|null $attributes
     */
    public function setAttributes(?array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return Price[]|null
     */
    public function getPrice(): ?array
    {
        return $this->price;
    }

    /**
     * @param Price[]|null $price
     */
    public function setPrice(?array $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Price[]|null
     */
    public function getOriginalPrice(): ?array
    {
        return $this->originalPrice;
    }

    /**
     * @param Price[]|null $originalPrice
     */
    public function setOriginalPrice(?array $originalPrice): self
    {
        $this->originalPrice = $originalPrice;

        return $this;
    }
}
