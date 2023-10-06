<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Upgrade\Module\Hooks\Shop\Cronjob;

use ClicShopping\OM\HTML;

use ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin\Cron;
use ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin\Marketplace;

class Process implements \ClicShopping\OM\Modules\HooksInterface
{
  public function __construct()
  {
  }

  /**
   *
   */
  private function cronJob(): void
  {
    $cron_id_marketplace = Cron::getCronCode('marketplace');

    if (isset($_GET['cronId'])) {
      $cron_id = HTML::sanitize($_GET['cronId']);

      Cron::updateCron($cron_id);

      if (isset($cron_id) && $cron_id_marketplace == $cron_id) {
        Marketplace::Cronjob();
      }
    } else {
      Cron::updateCron($cron_id_marketplace);

      if (isset($cron_id_marketplace)) {
        Marketplace::Cronjob();
      }
    }
  }

  public function execute()
  {
    $this->cronJob();
  }
}