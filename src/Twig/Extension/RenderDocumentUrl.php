<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Twig\Extension;

use MonsieurBiz\SyliusSearchPlugin\Helper\RenderDocumentUrlHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RenderDocumentUrl extends AbstractExtension
{
    public function __construct(
        private RenderDocumentUrlHelper $helper,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('search_result_url_param', [$this->helper, 'getUrlParams']),
        ];
    }
}
