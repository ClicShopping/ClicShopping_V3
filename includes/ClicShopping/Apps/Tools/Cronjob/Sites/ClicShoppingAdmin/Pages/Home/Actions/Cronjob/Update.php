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
  use ClicShopping\OM\HTML;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    public function __construct()
    {
      $this->app = Registry::get('Cronjob');
    }

    /**
     *
     */
    private function Update() :void
    {
      $cron_id = HTML::sanitize($_GET['cronId']);
      $code = HTML::sanitize($_POST['code']);
      $cycle = HTML::sanitize($_POST['cycle']);
      $action = HTML::sanitize($_POST['action']);

      $sql_data_array = [
        'code' => $code,
        'cycle' => $cycle,
        'action' => $action,
        'date_modified' => 'now()'
      ];

      $update_array = [
        'cron_id' => $cron_id
      ];

      $this->app->db->save('cron', $sql_data_array, $update_array);
    }
    
    
    public function execute()
    {
      if (isset($_GET['Update'])) {
        $this->Update();
      }

      $this->app->redirect('Cronjob');
    }
  }