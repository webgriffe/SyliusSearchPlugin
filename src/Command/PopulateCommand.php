<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Command;

use Exception;
use MonsieurBiz\SyliusSearchPlugin\Exception\ReadOnlyIndexException;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\Index\Indexer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateCommand extends Command
{
    protected static $defaultName = 'monsieurbiz:search:populate';

    /**
     * @psalm-suppress MixedArgument
     */
    public function __construct(private Indexer $documentIndexer)
    {
        parent::__construct(self::$defaultName);
    }

    /**
     * @throws Exception|\Elastica\Exception\ExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Starting generating index!');
        try {
            $this->documentIndexer->indexAll();
        } catch (ReadOnlyIndexException) {
            $output->writeln('Cannot purge old index. Please to do it manually if needed.');

            return self::FAILURE;
        }
        $output->writeln('Index generated!');

        return self::SUCCESS;
    }
}
