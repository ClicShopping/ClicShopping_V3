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

class ApiGetProduct
{
  /**
   * @param int|null $id
   * @param int|string $language_id
   * @return array
   */
  private static function products(int|string $id, int|string $language_id)
  {
    $CLICSHOPPING_Db = Registry::get('Db');

    if (is_numeric($id)) {
      $sql_request = ' and p.products_id = :products_id';
    } else {
      $sql_request = '';
    }

    if (is_numeric($language_id)) {
      $sql_language_request = ' and pd.language_id = :language_id';
    } else {
      $sql_language_request = '';
    }

    $Qapi = $CLICSHOPPING_Db->prepare('select p.*,
                                                pd.*
                                         from :table_products p,
                                              :table_products_description pd 
                                         where p.products_id = pd.products_id
                                         ' . $sql_request . '
                                         ' . $sql_language_request . '
                                      ');
    if (is_numeric($id)) {
      $Qapi->bindInt(':products_id', $id);
    }

    if (is_numeric($language_id)) {
      $Qapi->bindInt(':language_id', $language_id);
    }

    $Qapi->execute();

    $product_data = [];

    $result = $Qapi->fetchAll();

    foreach ($result as $value) {
      $product_data[] = [
        'products_id' => $value['products_id'],
        'language_id' => $value['language_id'],
        'products_name' => $value['products_name'],
        'products_description' => $value['products_description'],
        'products_model' => $value['products_model'],
        'products_quantity' => $value['products_quantity'],
        'products_weight' => $value['products_weight'],
        'products_quantity_alert' => $value['products_quantity_alert'],
        'products_sku' => $value['products_sku'],
        'products_upc' => $value['products_upc'],
        'products_ean' => $value['products_ean'],
        'products_jan' => $value['products_jan'],
        'products_isbn' => $value['products_isbn'],
        'products_mpn' => $value['products_mpn'],
        'products_price' => $value['products_price'],
        'products_dimension_width' => $value['products_dimension_width'],
        'products_dimension_height' => $value['products_dimension_height'],
        'products_dimension_depth' => $value['products_dimension_depth'],
        'products_volume' => $value['products_volume'],
      ];
    }

    return $product_data;
  }


  public function execute()
  {
    if (isset($_GET['pId'], $_GET['token'])) {
      $id = HTML::sanitize($_GET['pId']);

      if (isset($_GET['lId'])) {
        $language_id = HTML::sanitize($_GET['lId']);
      } else {
        $language_id = '';
      }

      return static::products($id, $language_id);
    } else {
      return false;
    }
  }
}
