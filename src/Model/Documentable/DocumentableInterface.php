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

namespace MonsieurBiz\SyliusSearchPlugin\Model\Documentable;

use JoliCode\Elastically\Mapping\MappingProviderInterface;

interface DocumentableInterface
{
    public function getIndexCode(): string;

    // TODO move it in CustomMappingProviderInterface
    public function setMappingProvider(MappingProviderInterface $mapping): void;

    public function getMappingProvider(): MappingProviderInterface;

//    dataSource
//
//    className => Product
}
