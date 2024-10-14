<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\Favorites;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Groups\Groups as GroupsApp;

class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  private mixed $app;

  public function __construct()
  {
    if (!Registry::exists('Groups')) {
      Registry::set('Groups', new GroupsApp());
    }

    $this->app = Registry::get('Groups');
  }

  public function execute()
  {
    if (isset($_GET['Insert'])) {
      if (isset($_POST['customers_group'])) {
        $customers_group_id = HTML::sanitize($_POST['customers_group']);

        $Qfavorites = $this->app->db->prepare('select products_favorites_id
                                                 from :table_products_favorites
                                                 order by products_favorites_id desc
                                                 limit 1
                                                ');
        $Qfavorites->execute();

        $sql_data_array = ['customers_group_id' => (int)$customers_group_id];

        $this->app->db->save('products_favorites', $sql_data_array, ['products_favorites_id' => (int)$Qfavorites->valueInt('products_favorites_id')]);
      }
    }
  }
}