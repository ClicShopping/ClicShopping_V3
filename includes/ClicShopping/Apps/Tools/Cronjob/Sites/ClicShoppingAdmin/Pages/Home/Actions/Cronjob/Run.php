<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Tools\Cronjob\Sites\ClicShoppingAdmin\Pages\Home\Actions\Cronjob;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin\Cron;

class Run extends \ClicShopping\OM\PagesActionsAbstract
{
  protected string $code;

  public function __construct()
  {
    $this->app = Registry::get('Cronjob');
    $this->id = HTML::sanitize($_GET['cronId']);
    $this->hooks = Registry::get('Hooks');
  }

  public function execute()
  {
    if (isset($this->id)) {
      $time = time();

      $results = Cron::getCrons(null, $this->id);

      foreach ($results as $result) {
        if (strtotime('+1 ' . $result['cycle'], strtotime($result['date_modified'])) < ($time + 10)) {
          Cron::updateCron($result['cron_id']);

          $this->hooks->call('Cronjob', 'Process');
        }
      }
    }

    $this->app->redirect('Cronjob');
  }
}