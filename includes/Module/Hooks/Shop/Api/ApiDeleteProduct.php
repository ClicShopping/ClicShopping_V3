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

class ApiDeleteProduct
{
  /**
   * Deletes a product and its related data from various tables in the database.
   *
   * @param int|string $id The ID of the product to be deleted. Can be either an integer or a string.
   * @return void
   */
  private static function deleteProducts(int|string $id)
  {
    $CLICSHOPPING_Db = Registry::get('Db');
    $CLICSHOPPING_Hooks = Registry::get('Hooks');

    $QcountProducts = $CLICSHOPPING_Db->prepare('select products_id,
                                                          categories_id
                                                   from :table_products_to_categories
                                                   where products_id = :products_id
                                                  ');

    $QcountProducts->bindInt(':products_id', $id);
    $QcountProducts->execute();

    while ($QcountProducts->fetch()) {
      $sql_array = [
        'products_id' => (int)$id,
        'categories_id' => $QcountProducts->valueInt('categories_id')
      ];

      $CLICSHOPPING_Db->delete('products_to_categories', $sql_array);
      $CLICSHOPPING_Db->delete('products_notifications', ['products_id' => $id]);
      $CLICSHOPPING_Db->delete('products', ['products_id' => (int)$id]);
      $CLICSHOPPING_Db->delete('products_description', ['products_id' => (int)$id]);
      $CLICSHOPPING_Db->delete('products_images', ['products_id' => (int)$id]);

      $Qdelete = $CLICSHOPPING_Db->prepare('delete
                                               from :table_customers_basket
                                               where products_id = :products_id
                                               or products_id like :products_id_att
                                            ');
      $Qdelete->bindInt(':products_id', (int)$id);
      $Qdelete->bindInt(':products_id_att', (int)$id . '{%');
      $Qdelete->execute();

      $Qdel = $CLICSHOPPING_Db->prepare('delete
                                            from :table_customers_basket_attributes
                                            where products_id = :products_id
                                            or products_id like :products_id_att
                                           ');
      $Qdel->bindInt(':products_id', (int)$id);
      $Qdel->bindInt(':products_id_att', (int)$id . '{%');
      $Qdel->execute();
    }

    $Qcheck = $CLICSHOPPING_Db->get('products_to_categories', 'products_id', ['products_id' => $id], null, 1);

    if ($Qcheck->fetch() === false) {
      $CLICSHOPPING_Hooks->call('Products', 'RemoveProduct');
    }
  }

  /**
   * Handles the execution of a product-related operation based on query parameters.
   *
   * @return mixed Returns a JSON-encoded error message if the ID format is invalid,
   *               the result of the deleteProducts method if the operation is successful,
   *               or false if required parameters are missing.
   */
  public function execute()
  {
    if (isset($_GET['pId'], $_GET['product'])) {
      $id = HTML::sanitize($_GET['pId']);

      if (!is_numeric($id)) {
        http_response_code(400);
        return json_encode(['error' => 'Invalid ID format']);
      }

      return static::deleteProducts($id);
    } else {
      return false;
    }
  }
}