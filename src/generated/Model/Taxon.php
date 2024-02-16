<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\generated\Model;

class Taxon
{
    protected ?string $name;

    protected ?string $code;

    protected ?int $position;

    protected ?int $level;

    protected ?int $productPosition;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getProductPosition(): ?int
    {
        return $this->productPosition;
    }

    public function setProductPosition(?int $productPosition): self
    {
        $this->productPosition = $productPosition;

        return $this;
    }
}
