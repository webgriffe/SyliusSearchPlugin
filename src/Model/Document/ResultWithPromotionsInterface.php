<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Model\Document;

/**
 * @deprecated Will be removed in next major release and method moved to ResultInterface
 */
interface ResultWithPromotionsInterface extends ResultInterface
{
    /**
     * @param string $channel
     * @param string $currency
     * @param int $value
     * @param string[] $appliedPromotions
     *
     * @return ResultInterface
     */
    public function addPriceWithPromotions(string $channel, string $currency, int $value, array $appliedPromotions = []): ResultInterface;
}
