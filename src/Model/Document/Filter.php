<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Document;

class Filter
{
    /** @var FilterValue[] */
    private array $values = [];

    public function __construct(
        private string $code,
        private string $label,
        private int $count,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return FilterValue[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function addValue(string $value, int $count): void
    {
        $this->values[] = new FilterValue($value, $count);
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
