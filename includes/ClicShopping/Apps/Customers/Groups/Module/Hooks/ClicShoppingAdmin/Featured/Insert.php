<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\Featured;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Customers\Groups\Groups as GroupsApp;

class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;

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

        $Qfeatured = $this->app->db->prepare('select products_featured_id
                                               from :table_products_featured
                                               order by products_featured_id desc
                                               limit 1
                                              ');
        $Qfeatured->execute();

        $sql_data_array = ['customers_group_id' => (int)$customers_group_id];

        $this->app->db->save('products_featured', $sql_data_array, ['products_featured_id' => (int)$Qfeatured->valueInt('products_featured_id')]);
      }
    }
  }
}