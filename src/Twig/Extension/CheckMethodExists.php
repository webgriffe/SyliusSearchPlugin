<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Twig\Extension;

use function array_key_exists;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

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

    /**
     * @psalm-suppress UndefinedDocblockClass
     */
    public function bundleExists(string $bundle): bool
    {
        /** @var string[] $bundles */
        $bundles = $this->container->getParameter('kernel.bundles');

        return array_key_exists(
            $bundle,
            $bundles,
        );
    }
}
