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

class ApiGetCategories
{
  /**
   * Retrieves category data based on the provided category ID and language ID.
   *
   * @param int|string $id The category ID. Can be an integer or a string.
   * @param int|string $language_id The language ID. Can be an integer or a string.
   * @return array An array containing category data, including categories ID, parent ID, language ID, name, and description.
   */
  private static function categories(int|string $id, int|string $language_id)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (is_numeric($id)) {
      $sql_request = ' and c.categories_id = :categories_id';
    } else {
      $sql_request = '';
    }

    if (is_numeric($language_id)) {
      $sql_language_request = ' and cd.language_id = :language_id';
    } else {
      $sql_language_request = '';
    }

    $Qapi = $CLICSHOPPING_Db->prepare('select c.*,
                                                cd.*
                                         from :table_categories c,
                                              :table_categories_description cd 
                                         where c.categories_id = cd.categories_id
                                         ' . $sql_request . '
                                         ' . $sql_language_request . '
                                      ');
    if (is_numeric($id)) {
      $Qapi->bindInt(':categories_id', $id);
    }

    if (is_numeric($language_id)) {
      $Qapi->bindInt(':language_id', $language_id);
    }

    $Qapi->execute();

    $categories_data = [];

    $result = $Qapi->fetchAll();

    foreach ($result as $value) {
      $categories_data[] = [
        'categories_id' => $value['categories_id'],
        'parent_id' => $value['parent_id'],
        'language_id' => $value['language_id'],
        'categories_name' => $value['categories_name'],
        'categories_description' => $value['categories_description'],
      ];
    }

    return $categories_data;
  }

  /**
   * Executes the required process based on the provided GET parameters.
   *
   * @return string|bool Returns a JSON-encoded string containing the categories data if the parameters are valid.
   *                     Returns a JSON-encoded error message if the language ID format is invalid.
   *                     Returns false if required parameters are missing.
   */
  public function execute()
  {
    $id = HTML::sanitize($_GET['cId']);

    if (isset($_GET['cId'], $_GET['token'])) {
      if (isset($_GET['lId'])) {
        $language_id = HTML::sanitize($_GET['lId']);
      } else {
        $language_id = '';
      }

      if (!is_numeric($language_id)) {
        http_response_code(400);
        return json_encode(['error' => 'Invalid ID format']);
      }

      return static::categories($id, $language_id);
    } else {
      return false;
    }
  }
}
