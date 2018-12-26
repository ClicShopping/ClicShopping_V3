<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Hash;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\ProductsAttributes\Classes\Shop\ProductsAttributesShop;

  class ShoppingCart {

    public $contents = [];
    public $total;
    protected $weight;
    protected $content_type;
    protected $min_quantity;
    protected $sub_total = 0;
    protected $quantity;
    protected $db;
    protected $lang;
    protected $customer;
    protected $productsCommon;
    protected $prod;
    protected $tax;
    protected $productsAttributes;

    public function __construct() {

      $this->db = Registry::get('Db');
      $this->lang = Registry::get('Language');
      $this->customer = Registry::get('Customer');
      $this->prod = Registry::get('Prod');
      $this->productsCommon = Registry::get('ProductsCommon');
      $this->tax = Registry::get('Tax');

      if (!Registry::exists('ProductsAttributesShop')) {
        Registry::set('ProductsAttributesShop', new ProductsAttributesShop());
      }

      $this->productsAttributes = Registry::get('ProductsAttributesShop');

       if ( !isset($_SESSION['ClicShoppingCart']) ) {
         $_SESSION['ClicShoppingCart'] = ['contents' => [],
                                          'sub_total_cost' => 0,
                                          'total_cost' => 0,
                                          'total_weight' => 0,
                                          'shipping_address' => ['zone_id' => STORE_ZONE,
                                                                 'country_id' => STORE_COUNTRY
                                                                ],
                                  ];

      }


      $this->contents =& $_SESSION['ClicShoppingCart']['contents'];

      $this->sub_total =& $_SESSION['ClicShoppingCart']['sub_total_cost'];
      $this->weight =& $_SESSION['ClicShoppingCart']['total_weight'];
    }


    public function shoppingCart() {
      $this->reset();
    }

/*
 *  Restore the good qty in function B2BC / B2C or not and qty minimal or maximal defined
 *  string $qty
*/

    private function getRestoreQty() {
      global $qty, $products_id;

      $qty = $this->getCheckGoodQty($products_id, $qty);

      return $qty;
    }

/*
 *  Restore the the cart content
 *
*/
    public function getRestoreContents() {
      if (!$this->customer->isLoggedOn()) return false;

// insert current cart contents in database
      if (is_array($this->contents)) {

        foreach (array_keys($this->contents) as $products_id ) {

// B2B / B2C Choose the good qty
          $qty = $this->contents[$products_id]['qty'];
          $qty1 = $this->getRestoreQty();

          if ($qty < $qty1) $qty = $this->getRestoreQty();
          if ($qty > $qty1) $qty = $this->contents[$products_id]['qty'];

          $Qcheck = $this->db->prepare('select products_id
                                         from :table_customers_basket
                                         where customers_id = :customers_id
                                         and products_id = :products_id'
                                      );

          $Qcheck->bindInt(':customers_id', $this->customer->getID());
          $Qcheck->bindValue(':products_id', $products_id);
          $Qcheck->execute();

          if ($Qcheck->fetch() === false) {
            $this->db->save('customers_basket', ['customers_id' => (int)$this->customer->getID(),
                                                  'products_id' => $products_id,
                                                  'customers_basket_quantity' => (int)$qty,
                                                  'customers_basket_date_added' => date('Ymd')
                                                ]
                            );

            if (isset($this->contents[$products_id]['attributes'])) {

              foreach ($this->contents[$products_id]['attributes'] as $option => $value) {
                $this->db->save('customers_basket_attributes', ['customers_id' =>  (int)$this->customer->getID(),
                                                                'products_id' => $products_id,
                                                                'products_options_id' => (int)$option,
                                                                'products_options_value_id' => (int)$value
                                                               ]
                               );
              }
            }
          } else {

            $this->db->save('customers_basket', ['customers_basket_quantity' => (int)$qty],
                                                ['customers_id' => (int)$this->customer->getID(),
                                                 'products_id' => $products_id
                                                ]
                            );
          }
        }
      }

// reset per-session cart contents, but not the database contents
      $this->reset(false);

      $Qproducts = $this->db->prepare('select products_id,
                                              customers_basket_quantity
                                       from :table_customers_basket
                                       where customers_id = :customers_id
                                      ');

      $Qproducts->bindInt(':customers_id', $this->customer->getID());

      $Qproducts->execute();

      while ($Qproducts->fetch()) {
        $this->contents[$Qproducts->value('products_id')] = ['qty' => $Qproducts->valueInt('customers_basket_quantity')];

// attributes
        $Qattributes = $this->db->prepare('select products_options_id,
                                                  products_options_value_id
                                            from :table_customers_basket_attributes
                                            where customers_id = :customers_id
                                            and products_id = :products_id
                                            ');

        $Qattributes->bindInt(':customers_id', $this->customer->getID());
        $Qattributes->bindValue(':products_id', $Qproducts->value('products_id'));
        $Qattributes->execute();

        while ($Qattributes->fetch()) {
          $this->contents[$Qproducts->value('products_id')]['attributes'][$Qattributes->valueInt('products_options_id')] = $Qattributes->valueInt('products_options_value_id');
        }
      }

      $this->cleanup();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

/**
 * remove all items
 * @param bool $reset_database
 */
    public function reset($reset_database = false) {
      if ($this->customer->isLoggedOn() && ($reset_database === true)) {
        $this->db->delete('customers_basket', ['customers_id' => (int)$this->customer->getID()]);

        $this->db->delete('customers_basket_attributes', ['customers_id' => (int)$this->customer->getID()]);
      }

      $this->contents = [];
      $this->sub_total = 0;
      $this->total = 0;
      $this->weight = 0;
      $this->content_type = false;

      unset($this->cartID);

      if (isset($_SESSION['cartID'])) {
        unset($_SESSION['cartID']);
      }
    }

/**
 * add products
 * @param $products_id
 * @param string $qty
 * @param string $attributes
 * @param bool $notify
 */
    public function add($products_id, $qty = '1', $attributes = '', $notify = true) {
      $products_id_string = $this->getUprid($products_id, $attributes);
      $products_id = $this->getPrid($products_id_string);

      $qty = $this->getCheckGoodQty($products_id, $qty);

      $attributes_pass_check = true;

      if (is_array($attributes) && !empty($attributes)) {
        foreach ($attributes as $option => $value) {
          if (!is_numeric($option) || !is_numeric($value)) {
            $attributes_pass_check = false;
            break;
          } else {
            $check = $this->productsAttributes->GetCheckProductsAttributes($products_id, $option, $value);

            if ($check === false) {
              $attributes_pass_check = false;
              break;
            }
          }
        }
//  } elseif (clic_has_product_attributes($products_id)) {
      } elseif ($this->productsCommon->getHasProductAttributes()) {
        $attributes_pass_check = false;
      }

      if (is_numeric($products_id) && is_numeric($qty) && ($attributes_pass_check === true)) {
        $Qcheck = $this->db->prepare('select products_id
                                      from :table_products
                                      where products_id = :products_id
                                      and products_status = 1
                                      and products_archive = 0
                                    ');

        $Qcheck->bindInt(':products_id', $products_id);
        $Qcheck->execute();

        if ($Qcheck->fetch() !== false) {
          if ($notify == true) {
            $_SESSION['new_products_id_inCart'] = $products_id;
          }

          if ($this->inCart($products_id_string)) {
            $this->updateQuantity($products_id_string, $qty, $attributes);
          } else {
            $this->contents[$products_id_string] = ['qty' => (int)$qty];

// insert into database
            if ($this->customer->isLoggedOn()) {

              $this->db->save('customers_basket', ['customers_id' => (int)$this->customer->getID(),
                                                   'products_id' => $products_id_string,
                                                   'customers_basket_quantity' => (int)$qty,
                                                   'customers_basket_date_added' => date('Ymd')
                                                  ]
                              );

            }

            if (is_array($attributes)) {
              foreach ($attributes as $option => $value) {
                $this->contents[$products_id_string]['attributes'][$option] = $value;
// insert into database
                if ($this->customer->isLoggedOn()) {

                  $this->db->save('customers_basket_attributes', ['customers_id' => (int)$this->customer->getID(),
                                                                  'products_id' => $products_id_string,
                                                                  'products_options_id' => (int)$option,
                                                                  'products_options_value_id' => (int)$value
                                                                 ]
                              );
                }
              }
            }
          }

          $this->cleanup();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
          $this->cartID = $this->generate_cart_id();
        }
      }
    }


    public function addCart($products_id, $qty = '1', $attributes = '', $notify = true) {
      $products_id_string = $this->getUprid($products_id, $attributes);
      $products_id = $this->getPrid($products_id_string);
// B2B / B2C Choose the good qty
      $qty = $this->getCheckGoodQty($products_id, $qty);

      $attributes_pass_check = true;

      if (is_array($attributes) && !empty($attributes)) {
        foreach ($attributes as $option => $value) {
          if (!is_numeric($option) || !is_numeric($value)) {
            $attributes_pass_check = false;
            break;
          } else {
            $check = $this->productsAttributes->GetCheckProductsAttributes($products_id, $option, $value);

            if ($check === false) {
              $attributes_pass_check = false;
              break;
            }
          }
        }
      } elseif ($this->productsCommon->getHasProductAttributes() === true) {
        $attributes_pass_check = false;
      }

      if (is_numeric($products_id) && is_numeric($qty) && ($attributes_pass_check === true)) {
        $Qcheck = $this->db->prepare('select products_id
                                      from :table_products
                                      where products_id = :products_id
                                      and products_status = 1
                                      and products_archive = 0
                                      ');

        $Qcheck->bindInt(':products_id', $products_id);
        $Qcheck->execute();

        if ($Qcheck->fetch() !== false) {
          if ($notify == true) {
            $_SESSION['new_products_id_inCart'] = $products_id;
          }

          if ($this->inCart($products_id_string)) {
            $this->updateQuantity($products_id_string, $qty, $attributes);
          } else {
            $this->contents[$products_id_string] = ['qty' => (int)$qty];

// insert into database
            if ($this->customer->isLoggedOn()) {
              $this->db->save('customers_basket', ['customers_id' => (int)$this->customer->getID(),
                                                    'products_id' => $products_id_string,
                                                    'customers_basket_quantity' => (int)$qty,
                                                    'customers_basket_date_added' => date('Ymd')
                                                  ]
                                );
            }

            if (is_array($attributes)) {
              foreach ($attributes as $option => $value) {
                $this->contents[$products_id_string]['attributes'][$option] = $value;
// insert into database
                if ($this->customer->isLoggedOn()) {
                  $this->db->save('customers_basket_attributes', ['customers_id' => (int)$this->customer->getID(),
                                                                  'products_id' => $products_id_string,
                                                                  'products_options_id' => (int)$option,
                                                                  'products_options_value_id' => (int)$value
                                                                 ]
                                  );
                }
              }
            }
          }

          $this->cleanup();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
          $this->cartID = $this->generate_cart_id();
        }
      }
    }

//************************************************
//Update
//************************************************
/**
 * Update product quantity
 * @param $products_id string
 * @param string $quantity int
 * @param string $attributes string
 */
    public function updateQuantity($products_id, $quantity = '', $attributes = '') {
      $products_id_string = $this->getUprid($products_id, $attributes);
      $products_id = $this->getPrid($products_id_string);

// Maximum to take an order
      if (defined('MAX_QTY_IN_CART') && (MAX_QTY_IN_CART > 0) && ((int)$quantity > MAX_QTY_IN_CART)) {
        $quantity = (int)MAX_QTY_IN_CART;
      }

// Define the minimum in basket if the qty min order is not define in product
      if ($this->getProductsMinOrderQtyShoppingCart($products_id) == 0) {
        if (defined('MAX_MIN_IN_CART') && (MAX_MIN_IN_CART > 0) && ((int)$quantity < MAX_MIN_IN_CART)) {
          $quantity = (int)MAX_MIN_IN_CART;
        }
      }

      $attributes_pass_check = true;

      if (is_array($attributes)) {
        foreach ($attributes as $option => $value) {
          if (!is_numeric($option) || !is_numeric($value)) {
            $attributes_pass_check = false;
            break;
          }
        }
      }

      if (is_numeric($products_id) && isset($this->contents[$products_id_string]) && is_numeric($quantity) && ($attributes_pass_check == true)) {
        $this->contents[$products_id_string] = ['qty' => (int)$quantity];
// update database
        if ($this->customer->isLoggedOn()) {
          $this->db->save('customers_basket', ['customers_basket_quantity' => (int)$quantity],
                                              ['customers_id' => (int)$this->customer->getID(),
                                               'products_id' => $products_id_string
                                              ]
                          );
        }

        if (is_array($attributes)) {
          foreach ($attributes as $option => $value) {
            $this->contents[$products_id_string]['attributes'][$option] = $value;
// update database
            if ($this->customer->isLoggedOn()) {
              $this->db->save('customers_basket_attributes', ['products_options_value_id' => (int)$value],
                                                             ['customers_id' => (int)$this->customer->getID(),
                                                              'products_id' => $products_id_string,
                                                              'products_options_id' => (int)$option
                                                             ]
                              );
            }
          }
        }

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
        $this->cartID = $this->generate_cart_id();
      }
    }

/**
 * items cleanup
 */
    public function cleanup() {
      foreach (array_keys($this->contents) as $key ) {
        if ($this->contents[$key]['qty'] < 1) {
          unset($this->contents[$key]);
// remove from database
          if ($this->customer->isLoggedOn()) {
            $this->db->delete('customers_basket', ['customers_id' => $this->customer->getID(),
                                                   'products_id' => $key
                                                  ]
                              );
            $this->db->delete('customers_basket_attributes', ['customers_id' => $this->customer->getID(),
                                                              'products_id' => $key
                                                             ]
                              );
          }
        }
      }
    }

/*
 * get total number of items in cart
 * @return : int sum of item number
 */
    public function getCountContents() {
      $total_items = 0;

      if (is_array($this->contents)) {
        foreach (array_keys($this->contents) as $products_id ) {
          $total_items += $this->getQuantity($products_id);
        }
      }

      return $total_items;
    }

/*
 * get total quantity on each items in cart
 * @param : int $products_id, id of the product
 * @return : int qty
 */
    public function getQuantity($products_id) {
      if (isset($this->contents[$products_id])) {
        return $this->contents[$products_id]['qty'];
      } else {
        return 0;
      }
    }


    public function inCart($products_id) {
      if (isset($this->contents[$products_id])) {
        return true;
      } else {
        return false;
      }
    }

/**
 * Remove item
 * @param $products_id
 */
    public function remove($products_id) {
      unset($this->contents[$products_id]);

// remove from database
      if ($this->customer->isLoggedOn()) {

        $this->db->delete('customers_basket', ['customers_id' => (int)$this->customer->getID(),
                                                'products_id' => $products_id
                                               ]
                            );

        $this->db->delete('customers_basket_attributes', ['customers_id' => (int)$this->customer->getID(),
                                                            'products_id' => $products_id
                                                          ]
                          );
      }

      $this->calculate();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

/**
 * Remove All Items
 */
    public function remove_all() {
      $this->reset();
    }

    public function get_product_id_list() {
      $product_id_list = '';
      if (is_array($this->contents)) {
       foreach (array_keys($this->contents) as $products_id ) {
          $product_id_list .= ', ' . $products_id;
        }
      }

      return substr($product_id_list, 2);
    }

    public function calculate() {
      $CLICSHOPPING_Weight = Registry::get('Weight');

      $this->total = 0;
      $this->weight = 0;
      if (!is_array($this->contents)) return 0;

      foreach (array_keys($this->contents) as $products_id) {
        $qty = $this->contents[$products_id]['qty'];

// Requete SQL pour avoir le prix du produit
        if ($this->customer->getCustomersGroupID() != 0) {
          $Qproduct = $this->db->prepare('select p.products_id,
                                                 p.products_price,
                                                 p.products_tax_class_id,
                                                 p.products_weight,
                                                 p.products_weight_class_id,
                                                 p.products_dimension_width,
                                                 p.products_dimension_height,
                                                 p.products_dimension_depth,
                                                 g.price_group_view,
                                                 g.customers_group_price
                                          from :table_products p left join :table_products_groups g on p.products_id = g.products_id
                                          where p.products_id = :products_id
                                          and g.customers_group_id = :customers_group_id
                                          and g.products_group_view = 1
                                         ');

          $Qproduct->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());
          $Qproduct->bindInt(':products_id', $products_id );
          $Qproduct->execute();

        } else {
          $Qproduct = $this->db->prepare('select products_id,
                                                 products_price,
                                                 products_tax_class_id,
                                                 products_weight,
                                                 products_weight_class_id,
                                                 products_dimension_width,
                                                 products_dimension_height,
                                                 products_dimension_depth
                                          from :table_products
                                          where products_id = :products_id
                                         ');

          $Qproduct->bindInt(':products_id', $products_id);
          $Qproduct->execute();
        }

        if ($Qproduct->fetch() !== false) {
          $prid = $Qproduct->valueInt('products_id');
          $products_tax =  $this->tax->getTaxRate($Qproduct->valueInt('products_tax_class_id'));
          $products_price = $Qproduct->valueDecimal('products_price');

          $products_weight = $CLICSHOPPING_Weight->convert($Qproduct->valueDecimal('products_weight'), $Qproduct->valueInt('products_weight_class_id'), SHIPPING_WEIGHT_UNIT);

          if (($this->customer->getCustomersGroupID() != 0) && ($Qproduct->valueInt('price_group_view') == 1)) {
            $products_price = $Qproduct->valueDecimal('customers_group_price');
          }

          if (($this->customer->getCustomersGroupID() != 0) && ($Qproduct->valueInt('price_group_view') == 1)) {

            $Qspecial = $this->db->prepare('select specials_new_products_price
                                            from :table_specials
                                            where products_id = :products_id
                                            and status = 1
                                            and customers_group_id = :customers_group_id
                                            ');

            $Qspecial->bindInt(':products_id', $prid );
            $Qspecial->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

          } else {

            $Qspecial = $this->db->prepare('select specials_new_products_price
                                            from :table_specials
                                            where products_id = :products_id
                                            and status = 1
                                           ');

            $Qspecial->bindInt(':products_id', $prid );
          }

          $Qspecial->execute();

          if ($Qspecial->fetch() !== false) {
            $products_price = $Qspecial->valueDecimal('specials_new_products_price');
          }

          $min_quantity = $this->productsCommon->getProductsMinimumQuantity($products_id);

// Total calculation
          if ($qty < (int)$min_quantity) $qty = (int)$min_quantity;

// product discount on quantity
          $new_price_with_discount_quantity = $this->productsCommon->getProductsNewPriceByDiscountByQuantity($products_id, $qty, $products_price);

          if ($new_price_with_discount_quantity > 0) {
            $products_price = $new_price_with_discount_quantity;
          }

// Probleme avec calculate price qui ne converti pas correctement les informations voir la classe shopping_cart
          $this->total +=  $this->tax->addTax($products_price, $products_tax) * $qty;
          $this->weight += ($qty * $products_weight);
        }

// attributes price
        if (isset($this->contents[$products_id]['attributes'])) {
          foreach ($this->contents[$products_id]['attributes'] as $option => $value) {

            $Qattributes = $this->db->prepare('select options_values_price,
                                                        price_prefix
                                                from :table_products_attributes
                                                where products_id = :products_id
                                                and options_id = :options_id
                                                and options_values_id = :options_values_id'
                                              );

            $Qattributes->bindInt(':products_id', $prid);
            $Qattributes->bindInt(':options_id', $option);
            $Qattributes->bindInt(':options_values_id', $value);
            $Qattributes->execute();

            if ($Qattributes->fetch() !== false) {
// La prix et l'attribut ((additionne) sont en rapport avec la quantite - total dela commande
              if ($Qattributes->value('price_prefix') == '+') {
                $this->total += $qty *  $this->tax->addTax($Qattributes->valueDecimal('options_values_price'), $products_tax);
              }

// La prix et l'attribut (soustraction) sont en rapport avec la quantite - total dela commande
              if ($Qattributes->value('price_prefix') == '-') {
                $this->total -= $qty *  $this->tax->addTax($Qattributes->valueDecimal('options_values_price'), $products_tax);
              }
            }
          }
        }
      }
    }


    public function get_products() {
      if (!is_array($this->contents)) return false;

      $products_array = [];

      foreach (array_keys($this->contents) as $products_id ) {
// Requete SQL pour avoir le prix du produit
        if ($this->customer->getCustomersGroupID() != 0) {
          $Qproducts = $this->db->prepare('select p.products_id,
                                                 pd.products_name,
                                                 p.products_model,
                                                 g.products_model_group,
                                                 p.products_image,
                                                 p.products_price,
                                                 p.products_weight,
                                                 p.products_weight_class_id,
                                                 p.products_dimension_width,
                                                 p.products_dimension_height,
                                                 p.products_dimension_depth,
                                                 p.products_tax_class_id,
                                                 g.price_group_view,
                                                 g.customers_group_price
                                          from :table_products p left join :table_products_groups g on p.products_id = g.products_id,
                                               :table_products_description pd
                                          where p.products_id = :products_id
                                          and pd.products_id = p.products_id
                                          and g.customers_group_id = :customers_group_id
                                          and g.products_group_view = 1
                                          and pd.language_id = :language_id
                                    ');

          $Qproducts->bindInt(':products_id', $products_id);
          $Qproducts->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());
          $Qproducts->bindInt(':language_id', $this->lang->getId() );
          $Qproducts->execute();

        } else {

          $Qproducts = $this->db->prepare('select p.products_id,
                                                 pd.products_name,
                                                 p.products_model,
                                                 p.products_image,
                                                 p.products_price,
                                                 p.products_weight,
                                                 p.products_weight_class_id,
                                                 p.products_dimension_width,
                                                 p.products_dimension_height,
                                                 p.products_dimension_depth,
                                                 p.products_tax_class_id
                                          from :table_products p,
                                               :table_products_description pd
                                          where p.products_id = :products_id
                                          and pd.products_id = p.products_id
                                          and pd.language_id = :language_id
                                    ');
          $Qproducts->bindInt(':products_id', $products_id);
          $Qproducts->bindInt(':language_id', (int)$this->lang->getId());
          $Qproducts->execute();
        }

        if ($Qproducts->fetch() !== false) {
          $prid = $Qproducts->valueInt('products_id');

// Prix B2B ou client normal
          if (($this->customer->getCustomersGroupID() != 0) && ($Qproducts->valueInt('price_group_view') == 1)) {
            $products_price = $Qproducts->valueDecimal('customers_group_price');
          } else {
            $products_price = $Qproducts->valueDecimal('products_price');
          }

// Prix promotionel B2B ou normal
          if (($this->customer->getCustomersGroupID() != 0) && ($Qproducts->valueInt('price_group_view') == 1)) {
            $Qspecial = $this->db->prepare('select specials_new_products_price
                                              from :table_specials
                                              where products_id = :products_id
                                              and status = 1
                                              and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                             ');
            $Qspecial->bindInt(':customers_group_id', $this->customer->getCustomersGroupID() );
            $Qspecial->bindInt(':products_id', $prid);
            $Qspecial->execute();
          } else {

            $Qspecial = $this->db->prepare('select specials_new_products_price
                                              from :table_specials
                                              where products_id = :products_id
                                              and status = 1
                                              and (customers_group_id = 0 or customers_group_id = 99)
                                             ');
            $Qspecial->bindInt(':products_id', $prid);
            $Qspecial->execute();
          }

          if ($Qspecial->fetch() !== false) {
            $products_price = $Qspecial->valueDecimal('specials_new_products_price');
          }

// product discount on quantity
          $new_price_with_discount_quantity = $this->productsCommon->getProductsNewPriceByDiscountByQuantity($products_id, $this->contents[$products_id]['qty'], $products_price);

          if ($new_price_with_discount_quantity > 0) {
            $products_price = $new_price_with_discount_quantity;
          }

          if ($Qproducts->value('products_model_group') != '' && $this->customer->getCustomersGroupID() != 0) {
            $model = $Qproducts->value('products_model_group');
          } else {
            $model = $Qproducts->value('products_model');
          }

          $products_array[] = ['id' => $products_id,
                              'name' => $Qproducts->value('products_name'),
                              'model' => $model,
                              'image' => $Qproducts->value('products_image'),
                              'price' => $products_price,
                              'quantity' => (int)$this->contents[$products_id]['qty'],
                              'weight' => $Qproducts->valueDecimal('products_weight'),
                              'products_weight_class_id' => (int)$Qproducts->valueint('products_weight_class_id'),
                              'products_dimension_width' => $Qproducts->valueDecimal('products_dimension_width'),
                              'products_dimension_height' => $Qproducts->valueDecimal('products_dimension_height'),
                              'products_dimension_depth' => $Qproducts->valueDecimal('products_dimension_depth'),
                              'final_price' =>($products_price + $this->productsAttributes->getAttributesPrice($products_id)),
                              'tax_class_id' => (int)$Qproducts->valueInt('products_tax_class_id'),
                              'attributes' => (isset($this->contents[$products_id]['attributes']) ? $this->contents[$products_id]['attributes'] : '')
                              ];
        }
      }

      return $products_array;
    }

    public function show_total() {
      $this->calculate();

      return $this->total;
    }

    public function show_weight() {
      $this->calculate();

      return $this->weight;
    }

    public function generate_cart_id($length = 5) {
      return Hash::getRandomString($length, 'digits');
    }

    public function get_content_type() {

      $this->content_type = false;

      if ( (DOWNLOAD_ENABLED == 'true') && ($this->getCountContents() > 0) ) {
        foreach (array_keys($this->contents) as $products_id ) {
          if (isset($this->contents[$products_id]['attributes'])) {
            foreach ($this->contents[$products_id]['attributes'] as $value) {
              $check = $this->productsAttributes->getCheckProductsDownload($products_id, $value);

              if ($check > 0) {
                switch ($this->content_type) {
                  case 'physical':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                   break;
                  default:
                    $this->content_type = 'virtual';
                  break;
                }
              } else {
                switch ($this->content_type) {
                  case 'virtual':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                  break;
                  default:
                    $this->content_type = 'physical';
                  break;
                }
              }
            }
          } else {
            switch ($this->content_type) {
              case 'virtual':
                $this->content_type = 'mixed';
                return $this->content_type;
              break;
              default:
                $this->content_type = 'physical';
              break;
            }
          }
        }
      } else {
        $this->content_type = 'physical';
      }

      return $this->content_type;
    }

    public function unserialize($broken) {
      foreach ($broken as $k => $v) {
        $kv = [$k, $v];
        $key=$kv['key'];
        if (gettype($this->$key) != 'user function')
          $this->$key=$kv['value'];
      }
    }

/**
 * Return a product ID with attributes
 * @param string $prid, $params
 * @return string $uprid,
 * @access public
 */

    public function getUprid($prid, $params) {
      if (is_numeric($prid)) {
        $uprid = (int)$prid;

        if (is_array($params) && (!empty($params))) {
          $attributes_check = true;
          $attributes_ids = '';

          foreach ($params as $option => $value) {
            if (is_numeric($option) && is_numeric($value)) {
              $attributes_ids .= '{' . (int)$option . '}' . (int)$value;
            } else {
              $attributes_check = false;
              break;
            }
          }

          if ($attributes_check === true) {
            $uprid .= $attributes_ids;
          }
        }
      } else {

        $uprid = $this->getPrid($prid);

        if (is_numeric($uprid)) {
          if (strpos($prid, '{') !== false) {
            $attributes_check = true;
            $attributes_ids = '';

// strpos()+1 to remove up to and including the first { which would create an empty array element in explode()
            $attributes = explode('{', substr($prid, strpos($prid, '{')+1));

            for ($i=0, $n=count($attributes); $i<$n; $i++) {
              $pair = explode('}', $attributes[$i]);

              if (is_numeric($pair[0]) && is_numeric($pair[1])) {
                $attributes_ids .= '{' . (int)$pair[0] . '}' . (int)$pair[1];
              } else {
                $attributes_check = false;
                break;
              }
            }

            if ($attributes_check === true) {
              $uprid .= $attributes_ids;
            }
          }
        } else {
          return false;
        }
      }

      return $uprid;
    }


/**
 * Return a product ID from a product ID with attributes
 * @param $uprid
 * @return bool|int
 */

    public function getPrid($uprid) {
      $pieces = explode('{', $uprid);

      if (is_numeric($pieces[0])) {
        return (int)$pieces[0];
      } else {
        return false;
      }
    }


    public function getCheckGoodQty($products_id, $qty) {
      if (defined('MAX_QTY_IN_CART') && (MAX_QTY_IN_CART > 0) && ((int)$qty > MAX_QTY_IN_CART)) {
        $qty = (int)MAX_QTY_IN_CART;
      }

      if (($this->getProductsMinOrderQtyShoppingCart($products_id) > 1) & ((int)$qty < $this->getProductsMinOrderQtyShoppingCart($products_id))) {
        $qty = $this->getProductsMinOrderQtyShoppingCart($products_id);
      }

      if ($this->getProductsMinOrderQtyShoppingCart($products_id) == 0) {
        if (defined('MAX_MIN_IN_CART') && (MAX_MIN_IN_CART > 0) && ((int)$qty < MAX_MIN_IN_CART)) {
          $qty = (int)MAX_MIN_IN_CART;
        }
      }

      return $qty;
    }

/**
 * display minimum order quantiy
 * @param int $products_id; id of the product
 * @return $min_order_qty_values, nimum order quantity
 * @access public
 */
    public function getProductsMinOrderQtyShoppingCart($products_id) {
      $products_id = $this->prod->getProductID($products_id);

      if ($this->customer->getCustomersGroupID()  == 0) {
        $QminOrderQty = $this->db->prepare('select products_min_qty_order
                                              from :table_products
                                              where products_id = :products_id
                                            ');
        $QminOrderQty->bindInt(':products_id', $products_id);

        $QminOrderQty->execute();
        $min_order_qty_values = $QminOrderQty->valueInt('products_min_qty_order');

      } else {

        $QcustomersGroupMinOrder = $this->db->prepare('select customers_group_quantity_default
                                                        from :table_customers_groups
                                                        where customers_group_id = :customers_group_id
                                                      ');
        $QcustomersGroupMinOrder->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

        $QcustomersGroupMinOrder->execute();

        $min_order_qty_values = $QcustomersGroupMinOrder->valueInt('customers_group_quantity_default');
      }

      return $min_order_qty_values;
    }
  }