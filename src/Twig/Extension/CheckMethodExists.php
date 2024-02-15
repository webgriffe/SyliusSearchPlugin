<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function array_key_exists;

class CheckMethodExists extends AbstractExtension
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('bundle_exists', [$this, 'bundleExists']),
        ];
    }

    public function bundleExists(string $bundle): bool
    {
        /** @var class-string[] $bundles */
        $bundles = $this->container->getParameter('kernel.bundles');
        return array_key_exists(
            $bundle,
            $bundles
        );
    }
}
