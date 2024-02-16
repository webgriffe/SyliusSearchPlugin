<?php

/*
 * This file is part of Monsieur Biz' Search plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Context;

use MonsieurBiz\SyliusSearchPlugin\Exception\TaxonNotFoundException;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class RequestTaxonContext implements TaxonContextInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private TaxonRepositoryInterface $taxonRepository,
        private LocaleContextInterface $localeContext
    ) {
    }

    public function getTaxon(): TaxonInterface
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        Assert::notNull($currentRequest);
        $slug = htmlspecialchars((string) $currentRequest->get('slug'));
        $localeCode = $this->localeContext->getLocaleCode();

        $taxon = $this->taxonRepository->findOneBySlug($slug, $localeCode);
        if (!$taxon instanceof TaxonInterface) {
            throw new TaxonNotFoundException();
        }

        return $taxon;
    }
}
