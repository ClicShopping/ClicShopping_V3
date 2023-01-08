<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\Cronjob\Sites\Shop\Pages\CJ;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin\Cron;

  class CJ extends \ClicShopping\OM\PagesAbstract
  {
    protected $file = null;
    protected bool $use_site_template = false;

    protected function init()
    {
      $CLICSHOPPING_Hooks = Registry::get('Hooks');
      $time = time();

      $results = Cron::getCrons(null, null);

      foreach ($results as $result) {
        if ($result['status'] == 1 && (strtotime('+1 ' . $result['cycle'], strtotime($result['date_modified'])) < ($time + 10))) {
          Cron::updateCron($result['cron_id']);

          $CLICSHOPPING_Hooks->call('Cronjob', 'Process');
        }
      }
    }
  }
