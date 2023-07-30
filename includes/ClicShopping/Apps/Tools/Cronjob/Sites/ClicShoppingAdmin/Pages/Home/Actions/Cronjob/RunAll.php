<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Tools\Cronjob\Sites\ClicShoppingAdmin\Pages\Home\Actions\Cronjob;

  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Tools\Cronjob\Classes\ClicShoppingAdmin\Cron;

  class RunAll extends \ClicShopping\OM\PagesActionsAbstract
  {
   protected string $code;

    public function __construct()
    {
      $this->app = Registry::get('Cronjob');
      $this->hooks = Registry::get('Hooks');
    }

    public function execute()
    {
      $time = time();

      $results = Cron::getCrons(null, null);

      foreach ($results as $result) {
        if ($result['status'] == 1 && (strtotime('+1 ' . $result['cycle'], strtotime($result['date_modified'])) < ($time + 10))) {
          Cron::updateCron($result['cron_id']);

          $this->hooks->call('Cronjob', 'Process');
        }
      }

      $this->app->redirect('Cronjob');
    }
  }