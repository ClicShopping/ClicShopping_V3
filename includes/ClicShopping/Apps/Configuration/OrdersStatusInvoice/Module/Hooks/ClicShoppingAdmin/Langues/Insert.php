<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Configuration\OrdersStatusInvoice\Module\Hooks\ClicShoppingAdmin\Langues;

use ClicShopping\OM\Registry;

use ClicShopping\Apps\Configuration\Langues\Classes\ClicShoppingAdmin\LanguageAdmin;
use ClicShopping\Apps\Configuration\OrdersStatusInvoice\OrdersStatusInvoice as OrdersStatusInvoiceApp;

class Insert implements \ClicShopping\OM\Modules\HooksInterface
{
  protected mixed $app;
  protected mixed $lang;

  public function __construct()
  {
    if (!Registry::exists('OrdersStatusInvoice')) {
      Registry::set('OrdersStatusInvoice', new OrdersStatusInvoiceApp());
    }

    $this->app = Registry::get('OrdersStatusInvoice');
    $this->lang = Registry::get('Language');
  }

  private function insert()
  {
    $insert_language_id = LanguageAdmin::getLatestLanguageID();

    $QordersStatusInvoice = $this->app->db->get('orders_status_invoice', '*', ['language_id' => $this->lang->getId()]);

    while ($QordersStatusInvoice->fetch()) {
      $cols = $QordersStatusInvoice->toArray();

      $cols['language_id'] = (int)$insert_language_id;

      $this->app->db->save('orders_status_invoice', $cols);
    }
  }

  public function execute()
  {
    if (isset($_GET['Langues'], $_GET['Insert'])) {
      $this->insert();
    }
  }
}