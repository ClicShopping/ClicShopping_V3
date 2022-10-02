<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Configuration\OrdersStatus\Sites\ClicShoppingAdmin\Pages\Home\Actions\OrdersStatus;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\Cache;

  class Update extends \ClicShopping\OM\PagesActionsAbstract
  {
    protected mixed $app;

    public function __construct()
    {
      $this->app = Registry::get('OrdersStatus');
      $this->hooks = Registry::get('Hooks');
    }

    public function execute()
    {
      $CLICSHOPPING_Language = Registry::get('Language');
      $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

      if (isset($_GET['oID'])) {
        $orders_status_id = HTML::sanitize($_GET['oID']);

        $languages = $CLICSHOPPING_Language->getLanguages();

        for ($i = 0, $n = \count($languages); $i < $n; $i++) {
          $orders_status_name_array = HTML::sanitize($_POST['orders_status_name']);
          $language_id = $languages[$i]['id'];

          $sql_data_array = [
            'orders_status_name' => HTML::sanitize($orders_status_name_array[$language_id]),
            'public_flag' => (isset($_POST['public_flag']) && ($_POST['public_flag'] == '1') ? '1' : '0'),
            'downloads_flag' => (isset($_POST['downloads_flag']) && ($_POST['downloads_flag'] == '1') ? '1' : '0'),
            'support_orders_flag' => (isset($_POST['support_orders_flag']) && ($_POST['support_orders_flag'] == '1') ? '1' : '0'),
            'authorize_to_delete_order' => (isset($_POST['authorize_to_delete_order']) && ($_POST['authorize_to_delete_order'] == '1') ? '1' : '0')
          ];

          $this->app->db->save('orders_status', $sql_data_array, [
              'orders_status_id' => (int)$orders_status_id,
              'language_id' => (int)$language_id
            ]
          );
        }

        if (isset($_POST['default'])) {
          $this->app->db->save('configuration', [
            'configuration_value' => $orders_status_id
          ], [
              'configuration_key' => 'DEFAULT_ORDERS_STATUS_ID'
            ]
          );
        }

        $this->hooks->call('OrdersStatus', 'UpdateOrdersStatusUpdate');

        Cache::clear('configuration');

        $this->app->redirect('OrdersStatus&page=' . $page . '&oID=' . $orders_status_id);
      } else {
        $this->app->redirect('OrdersStatus&page=' . $page);
      }
    }
  }