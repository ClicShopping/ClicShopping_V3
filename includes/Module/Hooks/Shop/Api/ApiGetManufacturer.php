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

class ApiGetManufacturer
{
  /**
   * @param int|string $id
   * @param int|string $language_id
   * @return array
   */
  private static function manufacturers(int|string $id, int|string $language_id): array
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (is_numeric($id)) {
      $sql_request = ' and s.manufacturers_id = :manufacturers_id';
    } else {
      $sql_request = '';
    }

    if (is_numeric($language_id)) {
      $sql_language_request = ' and si.languages_id = :language_id';
    } else {
      $sql_language_request = '';
    }

    $Qapi = $CLICSHOPPING_Db->prepare('select m.*,
                                                mi.*
                                         from :table_manufacturers m,
                                              :table_manufacturers_info mi
                                         where m.manufacturers_id = mi.manufacturers_id     
                                         ' . $sql_request . '
                                          ' . $sql_language_request . '
                                      ');
    if (is_numeric($id)) {
      $Qapi->bindInt(':manufacturers_id', $id);
    }

    if (is_numeric($language_id)) {
      $Qapi->bindInt(':language_id', $language_id);
    }

    $Qapi->execute();

    $manufacturers_data = [];

    $result = $Qapi->fetchAll();

    foreach ($result as $value) {
      $manufacturers_data[] = [
        'manufacturers_id' => $value['manufacturers_id'],
        'languages_id' => $value['languages_id'],
        'manufacturers_name' => $value['manufacturers_name'],
        'date_added' => $value['date_added'],
        'last_modified' => $value['last_modified'],
        'suppliers_id' => $value['suppliers_id'],
        'manufacturers_url' => $value['manufacturers_url'],
        'url_clicked' => $value['url_clicked'],
        'date_last_click' => $value['date_last_click'],
        'manufacturer_seo_title' => $value['manufacturer_seo_title'],
        'manufacturer_seo_keyword' => $value['manufacturer_seo_keyword'],
        'manufacturer_seo_description' => $value['manufacturer_seo_description'],
        'manufacturer_description' => $value['manufacturer_description'],
      ];
    }

    return $manufacturers_data;
  }

  public function execute()
  {

    if (isset($_GET['mId'], $_GET['token'])) {
      $id = HTML::sanitize($_GET['mId']);

      if (isset($_GET['lId'])) {
        $language_id = HTML::sanitize($_GET['lId']);
      } else {
        $language_id = '';
      }

      return static::manufacturers($id, $language_id);
    } else {
      return false;
    }
  }
}
