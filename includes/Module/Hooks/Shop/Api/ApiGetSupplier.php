<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\OM\Module\Hooks\Shop\Api;

use ClicShopping\OM\HTML;
use ClicShopping\OM\Registry;

class ApiGetSupplier
{
  /**
   * @param int|string $id
   * @param int|string $language_id
   * @return array
   */
  private static function suppliers(int|string $id, int|string $language_id): array
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (is_numeric($id)) {
      $sql_request = ' and s.suppliers_id = :suppliers_id';
    } else {
      $sql_request = '';
    }

    if (is_numeric($language_id)) {
      $sql_language_request = ' and si.languages_id = :language_id';
    } else {
      $sql_language_request = '';
    }

    $Qapi = $CLICSHOPPING_Db->prepare('select s.*,
                                                si.*
                                         from :table_suppliers s,
                                              :table_suppliers_info si
                                         where s.suppliers_id = si.suppliers_id     
                                         ' . $sql_request . '
                                          ' . $sql_language_request . '
                                      ');
    if (is_numeric($id)) {
      $Qapi->bindInt(':suppliers_id', $id);
    }

    if (is_numeric($language_id)) {
      $Qapi->bindInt(':language_id', $language_id);
    }

    $Qapi->execute();

    $suppliers_data = [];

    $result = $Qapi->fetchAll();

    foreach ($result as $value) {
      $suppliers_data[] = [
        'suppliers_id' => $value['suppliers_id'],
        'languages_id' => $value['languages_id'],
        'suppliers_name' => $value['suppliers_name'],
        'date_added' => $value['date_added'],
        'last_modified' => $value['last_modified'],
        'suppliers_manager' => $value['suppliers_manager'],
        'suppliers_phone' => $value['suppliers_phone'],
        'suppliers_email_address' => $value['suppliers_email_address'],
        'suppliers_fax' => $value['suppliers_fax'],
        'suppliers_address' => $value['suppliers_address'],
        'suppliers_suburb' => $value['suppliers_suburb'],
        'suppliers_postcode' => $value['suppliers_postcode'],
        'suppliers_city' => $value['suppliers_city'],
        'suppliers_states' => $value['suppliers_states'],
        'suppliers_country_id' => $value['suppliers_country_id'],
        'suppliers_notes' => $value['suppliers_notes'],
        'suppliers_status' => $value['suppliers_status'],
        'suppliers_url' => $value['suppliers_url'],
        'url_clicked' => $value['url_clicked'],
        'date_last_click' => $value['date_last_click'],
      ];
    }

    return $suppliers_data;
  }

  public function execute()
  {

    if (isset($_GET['sId'], $_GET['token'])) {
      $id = HTML::sanitize($_GET['sId']);

      if (isset($_GET['lId'])) {
        $language_id = HTML::sanitize($_GET['lId']);
      } else {
        $language_id = '';
      }

      return static::suppliers($id, $language_id);
    } else {
      return false;
    }
  }
}
