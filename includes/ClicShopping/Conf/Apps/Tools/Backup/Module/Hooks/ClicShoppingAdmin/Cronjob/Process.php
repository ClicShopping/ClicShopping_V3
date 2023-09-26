<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Backup\Module\Hooks\ClicShoppingAdmin\Cronjob;

use ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin\Cron;
use ClicShopping\OM\HTML;

use ClicShopping\Apps\Tools\Backup\Classes\ClicShoppingAdmin\Backup;

class Process implements \ClicShopping\OM\Modules\HooksInterface
{
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

  public function execute()
  {
    static::cronJob();
  }
}