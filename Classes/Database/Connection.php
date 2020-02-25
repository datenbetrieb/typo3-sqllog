<?php
declare(strict_types = 1);
namespace Datenbetrieb\Sqllog\Database;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Connection extends \TYPO3\CMS\Core\Database\Connection
{
  /**
   * overide the connect method in order to attach
   * an sql logger
   *
   * @return bool
   */
  public function connect(): bool
  {
      // Early return if the connection is already open and custom setup has been done.
      if (!parent::connect()) {
          return false;
      }
      $logger = GeneralUtility::makeInstance(Logging\TYPO3Logger::class);
      #$cache = new \Doctrine\Common\Cache\ArrayCache();
      $cache = new \Doctrine\Common\Cache\FilesystemCache('/tmp');
      $configuration = $this->getConfiguration();
      $configuration->setSQLLogger($logger);
      $configuration->setResultCacheImpl($cache);

      return true;
  }

  /**
   * Executes an, optionally parametrized, SQL query.
   *
   * If the query is parametrized, a prepared statement is used.
   * If an SQLLogger is configured, the execution is logged.
   *
   * @param string                 $query  The SQL query to execute.
   * @param mixed[]                $params The parameters to bind to the query, if any.
   * @param int[]|string[]         $types  The types the previous parameters are in.
   * @param QueryCacheProfile|null $qcp    The query cache profile, optional.
   *
   * @return ResultStatement The executed statement.
   *
   * @throws DBALException
   */
  public function executeQuery($query, array $params = [], $types = [], ?QueryCacheProfile $qcp = null)
  {
    //var_dump(strpos($query, 'SELECT'));
    $checkit =  'SELECT * FROM `pages` WHERE (`uid` = :dcValue1) AND (`pages`.`deleted` = 0)';
    //$checkit = 'SELECT * FROM `pages` WHERE';
    $length = 10000;
    $test = mt_rand(1, $length);
    $myrand = ($test<=0.8*$length);
    //$myrand = true;
    if (strpos($query, $checkit) === 0 && $myrand) {
      //var_dump(['qep for ', $query, $params]);
      $queryCacheProfile = new QueryCacheProfile(1000, md5($query . serialize($params)));
      $stmt = parent::executeQuery($query, $params, $types, $queryCacheProfile);
      $stmt->fetchAll();
      $stmt->closeCursor();
      return $stmt;
    } else {
      return parent::executeQuery($query, $params, $types, $qep);
    }
  }

}
