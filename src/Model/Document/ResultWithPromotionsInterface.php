<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Document;

/**
 * @deprecated Will be removed in next major release and method moved to ResultInterface
 */
interface ResultWithPromotionsInterface extends ResultInterface
{
    /**
     * @param string[] $appliedPromotions
     */
    public function addPriceWithPromotions(string $channel, string $currency, int $value, array $appliedPromotions = []): ResultInterface;
}
