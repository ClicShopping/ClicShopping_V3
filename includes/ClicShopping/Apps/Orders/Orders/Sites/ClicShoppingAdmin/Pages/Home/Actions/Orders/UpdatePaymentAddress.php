<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Orders\Orders\Sites\ClicShoppingAdmin\Pages\Home\Actions\Orders;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class UpdatePaymentAddress extends \ClicShopping\OM\PagesActionsAbstract
{
  private mixed $app;
  protected mixed $lang;
  private mixed $db;

  public function __construct()
  {
    $this->app = Registry::get('Orders');
    $this->lang = Registry::get('Language');
    $this->db = Registry::get('Db');
  }

  /**
   *
   */
  private function savePaymentOrderAddress(): void
  {
    $Qcountry = $this->app->db->get('countries', 'countries_name', ['countries_id' => HTML::sanitize($_POST['country'])]);
    $Qzones = $this->app->db->get('zones', 'zone_name', ['zone_id' => HTML::sanitize($_POST['state'])]);

    $sql_data_array = [
      'billing_name' => HTML::sanitize($_POST['billing_name']) ?? '',
      'billing_company' => HTML::sanitize($_POST['customers_company']) ?? '',
      'billing_street_address' => HTML::sanitize($_POST['billing_street_address']) ?? '',
      'billing_suburb' => HTML::sanitize($_POST['entry_suburb']) ?? '',
      'billing_postcode' => HTML::sanitize($_POST['entry_postcode']) ?? '',
      'billing_city' => HTML::sanitize($_POST['entry_city']) ?? '',
      'billing_state' => $Qzones->value('zone_name'),
      'billing_country' => $Qcountry->value('countries_name')
    ];

    $update_array = [
      'orders_id' => HTML::sanitize($_POST['order_id'])
    ];

    $this->app->db->save('orders', $sql_data_array, $update_array);
  }

  public function execute()
  {
    if (isset($_GET['UpdatePaymentAddress'])) {
      $this->savePaymentOrderAddress();

      $this->app->redirect('Edit&oID=' . HTML::sanitize($_POST['order_id']));
    }
  }
}
