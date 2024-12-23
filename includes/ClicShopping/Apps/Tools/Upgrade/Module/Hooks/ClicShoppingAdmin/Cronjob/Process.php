<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Upgrade\Module\Hooks\ClicShoppingAdmin\Cronjob;

use ClicShopping\OM\HTML;

use ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin\Cron;
use ClicShopping\Apps\Tools\Upgrade\Classes\ClicShoppingAdmin\Marketplace;

class Process implements \ClicShopping\OM\Modules\HooksInterface
{
  /**
   * Constructor method for the class.
   *
   * @return void
   */
  public function __construct()
  {
  }

  /**
   * Executes the cron job for marketplace functionality. It checks if a specific cron ID is provided
   * through the request and updates the corresponding cron status. If no specific ID is provided,
   * it defaults to updating and executing the marketplace cron job.
   *
   * @return void
   */
  private static function cronJob(): void
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

  /**
   * Executes the cron job by calling the static cronJob method.
   *
   * @return void
   */
  public function execute()
  {
    static::cronJob();
  }
}