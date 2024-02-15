<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model;

/** @phpstan-ignore-next-line */
class ArrayObject extends \ArrayObject
{
    public function toArray(): array
    {
        return $this->getArrayCopy();
    }
}
