<?php

namespace Datenbetrieb\Sqllog\Database\Logging;

use Psr\Log\LoggerAwareTrait;
use function microtime;

/**
 * Implements Doctrine SQLLogger for TYPO3
 */
class TYPO3Logger implements \Doctrine\DBAL\Logging\SQLLogger, \Psr\Log\LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * simple toggle to enable/disable logging
     *
     * @var bool
     */
    public $enabled = true;

    /** @var float|null */
    public $start = null;

    /** @var int */
    public $queryCount = 0;

    /** @var array */
    public $currentQuery = [];

    /**
     * {@inheritdoc}
     */
    public function startQuery($sql, ?array $params = null, ?array $types = null)
    {
        if (! $this->enabled) {
            return;
        }

        $this->start = microtime(true);
        $this->queryCount += 1;
        $this->currentQuery = ['sql' => $sql, 'params' => $params, 'types' => $types, 'executionMS' => 0];
    }

    /**
     * {@inheritdoc}
     */
    public function stopQuery()
    {
        if (! $this->enabled) {
            return;
        }

        $this->currentQuery['executionMS'] = microtime(true) - $this->start;
        $this->logger->debug("SQL-Metric", $this->currentQuery);
    }
}
