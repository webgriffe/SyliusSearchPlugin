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

    public function getFunctions()
    {
        return [
            new TwigFunction('bundle_exists', [$this, 'bundleExists']),
        ];
    }

    public function bundleExists(string $bundle): bool
    {
        /** @var array $bundles */
        $bundles = $this->container->getParameter('kernel.bundles');
        return array_key_exists(
            $bundle,
            $bundles
        );
    }
}
