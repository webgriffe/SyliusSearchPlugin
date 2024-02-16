<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Twig\Extension;

use MonsieurBiz\SyliusSearchPlugin\Form\Type\SearchType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;
use Webmozart\Assert\Assert;

class RenderSearchForm extends AbstractExtension
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private Environment $templatingEngine,
        private RequestStack $requestStack,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('search_form', [$this, 'createForm']),
        ];
    }

    public function createForm(string $template = null): Markup
    {
        $template = $template ?? '@MonsieurBizSyliusSearchPlugin/form.html.twig';

        $currentRequest = $this->requestStack->getCurrentRequest();
        Assert::notNull($currentRequest);

        return new Markup($this->templatingEngine->render($template, [
            'form' => $this->formFactory->create(SearchType::class)->createView(),
            'query' => urldecode((string) ($currentRequest->get('query') ?? '')),
        ]), 'UTF-8');
    }
}
