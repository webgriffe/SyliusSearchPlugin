<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model;

/**
 * @phpstan-ignore-next-line
 * @extends \ArrayObject<array-key, mixed>
 */
class ArrayObject extends \ArrayObject
{
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }
}
