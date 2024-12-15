<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
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
   * Retrieves a list of manufacturers and related details based on manufacturer ID and language ID.
   *
   * @param int|string $id The manufacturer ID to filter results. Can be an integer or a string.
   * @param int|string $language_id The language ID to filter results. Can be an integer or a string.
   * @return array An array containing manufacturer details such as ID, name, URL, SEO information, and description.
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

  /**
   * Executes the main functionality to retrieve manufacturer data based on provided inputs.
   *
   * This method checks if required query parameters are set and sanitizes their values.
   * If no language ID is provided, it defaults to an empty string. The method additionally
   * validates the language ID to ensure it is numeric. If the validation fails, a 400 HTTP
   * response code is sent with an error message. Otherwise, it retrieves manufacturer data
   * via the static manufacturers method.
   *
   * @return string|false Returns a JSON-encoded string containing manufacturers' data
   *                      if successful, a JSON-encoded error message if the validation
   *                      fails, or false if required parameters are missing.
   */
  public function execute()
  {

    if (isset($_GET['mId'], $_GET['token'])) {
      $id = HTML::sanitize($_GET['mId']);

      if (isset($_GET['lId'])) {
        $language_id = HTML::sanitize($_GET['lId']);
      } else {
        $language_id = '';
      }

      if (!is_numeric($language_id)) {
        http_response_code(400);
        return json_encode(['error' => 'Invalid ID format']);
      }

      return static::manufacturers($id, $language_id);
    } else {
      return false;
    }
  }
}
