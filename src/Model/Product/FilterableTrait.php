<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Product;

use Doctrine\ORM\Mapping as ORM;

trait FilterableTrait
{
    /**
     * @ORM\Column(name="filterable", type="boolean", nullable=false, options={"default"=true})
     */
    protected bool $filterable = true;

    public function isFilterable(): bool
    {
        return $this->filterable;
    }

    public function setFilterable(bool $filterable): void
    {
        $this->filterable = $filterable;
    }
}
