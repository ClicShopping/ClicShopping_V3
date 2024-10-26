<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Orders\ReturnOrders\Sites\ClicShoppingAdmin\Pages\Home\Actions\OrdersAction;

use ClicShopping\OM\Cache;
use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class Update extends \ClicShopping\OM\PagesActionsAbstract
{
  public mixed $app;

  public function __construct()
  {
    $this->app = Registry::get('ReturnOrders');
    $this->hooks = Registry::get('Hooks');
  }

  public function execute()
  {
    $CLICSHOPPING_Language = Registry::get('Language');
    $page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;

    if (isset($_GET['oID'])) {
      $return_action_id = HTML::sanitize($_GET['oID']);

      $languages = $CLICSHOPPING_Language->getLanguages();

      for ($i = 0, $n = \count($languages); $i < $n; $i++) {
        $name_array = HTML::sanitize($_POST['name']);
        $language_id = $languages[$i]['id'];

        $sql_data_array = [
          'name' => HTML::sanitize($name_array[$language_id]),
        ];

        $this->app->db->save('return_orders_action', $sql_data_array, [
            'return_action_id' => (int)$return_action_id,
            'language_id' => (int)$language_id
          ]
        );
      }

      $this->hooks->call('ReturnOrders', 'UpdateOrdersAction');

      Cache::clear('configuration');

      $this->app->redirect('OrdersAction&page=' . $page . '&oID=' . $return_action_id);
    } else {
      $this->app->redirect('OrdersAction&page=' . $page);
    }
  }
}