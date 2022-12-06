<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\Api\Sites\ClicShoppingAdmin\Pages\Home\Actions\Api;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  
  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('Api');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

      if (isset($_GET['cID'])) {
        $api_id = HTML::sanitize($_GET['cID']);
      } else {
        $api_id = null;
      }

     if (!\is_null($api_id)) {
        $username = HTML::sanitize($_POST['username']);
        $api_key = HTML::sanitize($_POST['api_key']);

        $sql_data_array = [
          'username' => $username,
          'api_key' => $api_key,
          'date_modified' => 'now()'
        ];


        $this->app->db->save('api', $sql_data_array, ['api_id' => (int)$api_id]);
      }

      $this->app->redirect('Api&page=' . $page . '&cID=' . $api_id . '&#tab2');
    }
  }