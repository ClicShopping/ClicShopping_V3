<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Customers\Groups\Module\Hooks\ClicShoppingAdmin\BannerManager;

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
      if (isset($_POST['customers_groups'])) {
        $customers_group_id = HTML::sanitize($_POST['customers_groups']);

        $Qbanners = $this->app->db->prepare('select banners_id
                                               from :table_banners
                                               order by banners_id desc
                                               limit 1
                                              ');
        $Qbanners->execute();

        $sql_data_array = ['customers_group_id' => (int)$customers_group_id];

        $this->app->db->save('banners', $sql_data_array, ['banners_id' => (int)$Qbanners->valueInt('banners_id')]);
      }
    }
  }
}