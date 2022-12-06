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

  class DeleteIP extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('Api');
    }

    public function execute()
    {
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

      if (isset($_GET['DeleteIP'])) {
        $api_ip_id = HTML::sanitize($_GET['dID']);

        $this->app->db->delete('api_ip', ['api_ip_id' => (int)$api_ip_id]);
      }

      $this->app->redirect('Edit&cID=' . (int)$_GET['cID'] .'&page=' . $page . '&#tab2');
    }
  }