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
      $this->getConfiguration()->setSQLLogger($logger);
      return true;
  }
}
