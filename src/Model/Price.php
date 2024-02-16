<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model;

class Price
{
    protected ?string $channel = null;

    protected ?string $currency = null;

    protected ?int $value = null;

    /** @var string[]|null */
    protected ?array $appliedPromotions = null;

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getAppliedPromotions(): ?array
    {
        return $this->appliedPromotions;
    }

    /**
     * @param string[]|null $appliedPromotions
     */
    public function setAppliedPromotions(?array $appliedPromotions): self
    {
        $this->appliedPromotions = $appliedPromotions;

        return $this;
    }
}
