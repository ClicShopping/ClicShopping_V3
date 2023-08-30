<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\OrdersStatus\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\Apps\Configuration\OrdersStatus\OrdersStatus as OrdersStatusApp;
use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin\LanguageAdmin;

class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;
  protected mixed $lang;

  public function __construct()
  {
    if (!Registry::exists('OrdersStatus')) {
      Registry::set('OrdersStatus', new OrdersStatusApp());
    }

    $this->app = Registry::get('OrdersStatus');
    $this->lang = Registry::get('Language');
  }

  private function insert()
  {
    $insert_language_id = LanguageAdmin::getLatestLanguageID();

    $QordersStatus = $this->app->db->get('orders_status', '*', ['language_id' => $this->lang->getId()]);

    while ($QordersStatus->fetch()) {
      $cols = $QordersStatus->toArray();

      $cols['language_id'] = (int)$insert_language_id;

      $this->app->db->save('orders_status', $cols);
    }
  }

  public function execute()
  {
    if (!\defined('CLICSHOPPING_APP_ORDERS_STATUS_OU_STATUS') || CLICSHOPPING_APP_ORDERS_STATUS_OU_STATUS == 'False') {
      return false;
    }

    if (isset($_GET['Langues'], $_GET['Insert'])) {
      $this->insert();
    }
  }
}