<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\Products\Classes\Shop;

  use ClicShopping\OM\HTML;

  class Prod
  {
    protected $products_id;
    protected $id;

    Public function __construct()
    {
    }

    /**
     * get this if of the products
     * @return null|int products_id
     */
    public function getID()
    {
// products description
      $id = empty($_GET['products_id']) ? null : HTML::sanitize($_GET['products_id']);

// products listing
      if (empty($id) && !isset($_GET['Search']) && !isset($_GET['Q'])) {
        if (isset($_POST['products_id']) && is_numeric($_POST['products_id']) && !empty(HTML::sanitize($_POST['products_id']))) {
          $id = empty($_POST['products_id']) ? null : HTML::sanitize($_POST['products_id']);
        }
      } elseif (isset($_GET['Search']) && isset($_GET['Q'])) {
        if (isset($_POST['products_id']) && is_numeric($_POST['products_id']) && !empty(HTML::sanitize($_POST['products_id']))) {
          $id = HTML::sanitize($_POST['products_id']);
        }
      }

      return $id;
    }

    /**
     * Generate a product ID string value containing its product attributes combinations
     *
     * @param string $id The product ID
     * @param array $params An array of product attributes
     * @return string
     */

    public static function getProductIDString(string $id, $params)
    {
      $string = $id;

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
     * @access public
     */

    public static function getProductID(string $id)
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
     *
     * @param string $field ,field of products, $direction, ascending descending
     * @access public
     *
     */
    public function setSortBy(string $field, string $direction = '+')
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

    public function setSortByDirection(string $direction)
    {
      $this->_sort_by_direction = ($direction == '-') ? '-' : '+';
    }
  }