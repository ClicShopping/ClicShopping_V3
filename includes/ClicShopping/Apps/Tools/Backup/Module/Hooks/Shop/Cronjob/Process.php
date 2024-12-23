<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Backup\Module\Hooks\Shop\Cronjob;

use ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin\Cron;
use ClicShopping\OM\HTML;

use ClicShopping\Apps\Tools\Backup\Classes\ClicShoppingAdmin\Backup;

class Process implements \ClicShopping\OM\Modules\HooksInterface
{
  /**
   * Executes the scheduled cron job for performing backup operations.
   * The method retrieves the cron code for the 'backup' process, then checks for a provided 'cronId' in
   * the GET request. If a valid 'cronId' matches the backup cron code, or no 'cronId' is provided
   * but the backup cron code exists, the backup process is triggered.
   *
   * @return void
   */
  private static function cronJob(): void
  {
    $cron_id_gdpr = Cron::getCronCode('backup');

    if (isset($_GET['cronId'])) {
      $cron_id = HTML::sanitize($_GET['cronId']);

      Cron::updateCron($cron_id);

      if (isset($cron_id) && $cron_id_gdpr == $cron_id) {
        Backup::backupNow();
      }
    } else {
      Cron::updateCron($cron_id_gdpr);

      if (isset($cron_id_gdpr)) {
        Backup::backupNow();
      }
    }
  }

  /**
   * Executes the main logic by invoking the cronJob method.
   *
   * @return void
   */
  public function execute()
  {
    static::cronJob();
  }
}