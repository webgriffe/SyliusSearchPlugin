<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Fixture\Factory;

use Exception;
use MonsieurBiz\SyliusSearchPlugin\Entity\Product\FilterableInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterableFixtureFactory extends AbstractExampleFactory implements FilterableFixtureFactoryInterface
{
    private OptionsResolver $optionsResolver;

    /**
     * @param RepositoryInterface<ProductAttributeInterface> $productAttributeRepository
     * @param RepositoryInterface<ProductOptionInterface> $productOptionRepository
     */
    public function __construct(
        protected RepositoryInterface $productAttributeRepository,
        protected RepositoryInterface $productOptionRepository,
    ) {
        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);
    }

    /**
     * @psalm-suppress InvalidArgument
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('attribute', null)
                ->setAllowedTypes('attribute', ['null', 'string', ProductAttributeInterface::class])
                ->setNormalizer('attribute', LazyOption::findOneBy($this->productAttributeRepository, 'code'))
            ->setDefault('option', null)
                ->setAllowedTypes('option', ['null', 'string', ProductOptionInterface::class])
                ->setNormalizer('option', LazyOption::findOneBy($this->productOptionRepository, 'code'))
            ->setDefault('filterable', true)
        ;
    }

    /**
     * @throws Exception
     */
    public function create(array $options = []): object
    {
        $options = $this->optionsResolver->resolve($options);

        if (isset($options['attribute'])) {
            $object = $options['attribute'];
        } elseif (isset($options['option'])) {
            $object = $options['option'];
        } else {
            throw new Exception('You need to specify an attribute or an option to be filterable.');
        }

        if (!$object instanceof FilterableInterface) {
            throw new Exception(sprintf('Your class "%s" is not an instance of %s', \get_class($object), FilterableInterface::class));
        }

        $object->setFilterable(($options['filterable']) ?? false);

        return $object;
    }
}
