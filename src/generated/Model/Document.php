<?php

namespace MonsieurBiz\SyliusSearchPlugin\generated\Model;

class Document
{
    /**
     * 
     *
     * @var string|null
     */
    protected $type;
    /**
     * 
     *
     * @var string|null
     */
    protected $code;
    /**
     * 
     *
     * @var int|null
     */
    protected $id;
    /**
     * 
     *
     * @var bool|null
     */
    protected $enabled;
    /**
     * 
     *
     * @var bool|null
     */
    protected $inStock;
    /**
     * 
     *
     * @var string|null
     */
    protected $slug;
    /**
     * 
     *
     * @var string|null
     */
    protected $image;
    /**
     * 
     *
     * @var string[]|null
     */
    protected $channel;
    /**
     * 
     *
     * @var Taxon|null
     */
    protected $mainTaxon;
    /**
     * 
     *
     * @var Taxon[]|null
     */
    protected $taxon;
    /**
     * 
     *
     * @var Attributes[]|null
     */
    protected $attributes;
    /**
     * 
     *
     * @var Price[]|null
     */
    protected $price;
    /**
     * 
     *
     * @var Price[]|null
     */
    protected $originalPrice;
    /**
     * 
     *
     * @return string|null
     */
    public function getType() : ?string
    {
        return $this->type;
    }
    /**
     * 
     *
     * @param string|null $type
     *
     * @return self
     */
    public function setType(?string $type) : self
    {
        $this->type = $type;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getCode() : ?string
    {
        return $this->code;
    }
    /**
     * 
     *
     * @param string|null $code
     *
     * @return self
     */
    public function setCode(?string $code) : self
    {
        $this->code = $code;
        return $this;
    }
    /**
     * 
     *
     * @return int|null
     */
    public function getId() : ?int
    {
        return $this->id;
    }
    /**
     * 
     *
     * @param int|null $id
     *
     * @return self
     */
    public function setId(?int $id) : self
    {
        $this->id = $id;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getEnabled() : ?bool
    {
        return $this->enabled;
    }
    /**
     * 
     *
     * @param bool|null $enabled
     *
     * @return self
     */
    public function setEnabled(?bool $enabled) : self
    {
        $this->enabled = $enabled;
        return $this;
    }
    /**
     * 
     *
     * @return bool|null
     */
    public function getInStock() : ?bool
    {
        return $this->inStock;
    }
    /**
     * 
     *
     * @param bool|null $inStock
     *
     * @return self
     */
    public function setInStock(?bool $inStock) : self
    {
        $this->inStock = $inStock;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getSlug() : ?string
    {
        return $this->slug;
    }
    /**
     * 
     *
     * @param string|null $slug
     *
     * @return self
     */
    public function setSlug(?string $slug) : self
    {
        $this->slug = $slug;
        return $this;
    }
    /**
     * 
     *
     * @return string|null
     */
    public function getImage() : ?string
    {
        return $this->image;
    }
    /**
     * 
     *
     * @param string|null $image
     *
     * @return self
     */
    public function setImage(?string $image) : self
    {
        $this->image = $image;
        return $this;
    }
    /**
     * 
     *
     * @return string[]|null
     */
    public function getChannel() : ?array
    {
        return $this->channel;
    }
    /**
     * 
     *
     * @param string[]|null $channel
     *
     * @return self
     */
    public function setChannel(?array $channel) : self
    {
        $this->channel = $channel;
        return $this;
    }
    /**
     * 
     *
     * @return Taxon|null
     */
    public function getMainTaxon() : ?Taxon
    {
        return $this->mainTaxon;
    }
    /**
     * 
     *
     * @param Taxon|null $mainTaxon
     *
     * @return self
     */
    public function setMainTaxon(?Taxon $mainTaxon) : self
    {
        $this->mainTaxon = $mainTaxon;
        return $this;
    }
    /**
     * 
     *
     * @return Taxon[]|null
     */
    public function getTaxon() : ?array
    {
        return $this->taxon;
    }
    /**
     * 
     *
     * @param Taxon[]|null $taxon
     *
     * @return self
     */
    public function setTaxon(?array $taxon) : self
    {
        $this->taxon = $taxon;
        return $this;
    }
    /**
     * 
     *
     * @return Attributes[]|null
     */
    public function getAttributes() : ?array
    {
        return $this->attributes;
    }
    /**
     * 
     *
     * @param Attributes[]|null $attributes
     *
     * @return self
     */
    public function setAttributes(?array $attributes) : self
    {
        $this->attributes = $attributes;
        return $this;
    }
    /**
     * 
     *
     * @return Price[]|null
     */
    public function getPrice() : ?array
    {
        return $this->price;
    }
    /**
     * 
     *
     * @param Price[]|null $price
     *
     * @return self
     */
    public function setPrice(?array $price) : self
    {
        $this->price = $price;
        return $this;
    }
    /**
     * 
     *
     * @return Price[]|null
     */
    public function getOriginalPrice() : ?array
    {
        return $this->originalPrice;
    }
    /**
     * 
     *
     * @param Price[]|null $originalPrice
     *
     * @return self
     */
    public function setOriginalPrice(?array $originalPrice) : self
    {
        $this->originalPrice = $originalPrice;
        return $this;
    }
}