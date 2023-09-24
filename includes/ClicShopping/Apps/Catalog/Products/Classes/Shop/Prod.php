<?php
/**
 *
 * @copyright 2008 - https://www.clicshopping.org
 * @Brand : ClicShoppingAI(TM) at Inpi all right Reserved
 * @Licence GPL 2 & MIT
 * @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

namespace ClicShopping\Apps\Catalog\Products\Classes\Shop;

use ClicShopping\OM\HTML;
use function is_array;

class Prod
{
  /**
   * get this if of the products
   * @return null|int products_id
   */
  public function getID()
  {
// products description
    if (isset($_GET['Id'])) {
      $id = empty($_GET['Id']) ? null : HTML::sanitize($_GET['Id']);
    } else {
      $id = empty($_GET['products_id']) ? null : HTML::sanitize($_GET['products_id']);
    }
// products listing
    if (empty($id) && !isset($_GET['Search']) && !isset($_GET['Q'])) {
      if (isset($_POST['Id']) && is_numeric($_POST['Id']) && !empty(HTML::sanitize($_POST['Id']))) {
        $id = empty($_POST['Id']) ? null : HTML::sanitize($_POST['Id']);
      } elseif (isset($_POST['products_id']) && is_numeric($_POST['products_id']) && !empty(HTML::sanitize($_POST['products_id']))) {
        $id = empty($_POST['products_id']) ? null : HTML::sanitize($_POST['products_id']);
      }
    } elseif (isset($_GET['Search'], $_GET['Q'])) {
      if (isset($_POST['Id']) && is_numeric($_POST['Id']) && !empty(HTML::sanitize($_POST['Id']))) {
        $id = HTML::sanitize($_POST['Id']);
      } elseif (isset($_POST['products_id']) && is_numeric($_POST['products_id']) && !empty(HTML::sanitize($_POST['products_id']))) {
        $id = HTML::sanitize($_POST['products_id']);
      }
    }

    return $id;
  }

  /**
   * Generate a product ID string value containing its product attributes combinations
   *
   * @param string $string
   * @param array $params An array of product attributes
   * @return string
   */

  public static function getProductIDString(string $string, $params): string
  {
    if (is_array($params) && !empty($params)) {
      $attributes_check = true;
      $attributes_ids = [];

      foreach ($params as $option => $value) {
        if (is_numeric($option) && is_numeric($value)) {
          $attributes_ids[] = (int)$option . '}' . (int)$value;
        } else {
          $attributes_check = false;
          break;
        }
      }

      if ($attributes_check === true) {
        $string .= '{' . implode(';', $attributes_ids);
      }
    }

    return $string;
  }

  /**
   * Generate a numeric product ID without product attribute combinations
   *
   * @param string $id The product ID
   * @return int
   */

  public static function getProductID(string $id): int
  {
    if (is_numeric($id)) {
      return $id;
    }

    $id = HTML::sanitize($id);

    $product = explode('{', $id, 2);

    return (int)$product[0];
  }

  /**
   * Products  sort by
   * @param string $field ,field of products, $direction, ascending descending
   */
  public function setSortBy(string $field, string $direction = '+'): void
  {
    switch ($field) {
      case 'model':
        $this->_sort_by = 'p.products_model';
        break;
      case 'manufacturer':
        $this->_sort_by = 'm.manufacturers_name';
        break;
      case 'quantity':
        $this->_sort_by = 'p.products_quantity';
        break;
      case 'weight':
        $this->_sort_by = 'p.products_weight';
        break;
      case 'price':
        $this->_sort_by = 'p.products_price';
        break;
      case 'date_added':
        $this->_sort_by = 'p.products_date_added';
        break;
    }

    $this->_sort_by_direction = ($direction == '-') ? '-' : '+';
  }

  /**
   * @param string $direction
   */
  public function setSortByDirection(string $direction): void
  {
    $this->_sort_by_direction = ($direction == '-') ? '-' : '+';
  }
}