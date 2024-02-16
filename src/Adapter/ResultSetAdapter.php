<?php

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Adapter;

use function array_slice;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\ResultInterface;
use MonsieurBiz\SyliusSearchPlugin\Model\Document\ResultSet;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements AdapterInterface<ResultInterface>
 */
class ResultSetAdapter implements AdapterInterface
{
    public function __construct(private ResultSet $resultSet)
    {
    }

    public function getResultSet(): ResultSet
    {
        return $this->resultSet;
    }

    public function getNbResults(): int
    {
        return $this->resultSet->getTotalHits();
    }

    public function getSlice($offset, $length): iterable
    {
        return array_slice($this->resultSet->getResults(), $offset, $length);
    }
}
