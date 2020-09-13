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

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Hash;
  use ClicShopping\OM\Registry;

  use ClicShopping\Apps\Catalog\ProductsAttributes\Classes\Shop\ProductsAttributesShop;

  class ShoppingCart
  {
    public array $contents = [];
    public float $total;
    public float $sub_total = 0;
    public $cartID;
    protected float $weight;
    protected string $content_type;
    protected int $min_quantity;
    protected int $quantity;
    protected string $productsId;
    protected bool $products_in_stock = true;

    protected $db;
    protected $lang;
    protected $customer;
    protected $productsCommon;
    protected $prod;
    protected $tax;
    protected $productsAttributes;
    protected $order_totals;

    public function __construct()
    {
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

      if (!isset($_SESSION['ClicShoppingCart'])) {
        $_SESSION['ClicShoppingCart'] = [
          'contents' => [],
          'sub_total_cost' => 0,
          'total_cost' => 0,
          'total_weight' => 0,
          'shipping_address' => [
            'zone_id' => STORE_ZONE,
            'country_id' => STORE_COUNTRY
          ],
          'order_totals' => []
        ];
      }

      $this->contents =& $_SESSION['ClicShoppingCart']['contents'];
      $this->sub_total =& $_SESSION['ClicShoppingCart']['sub_total_cost'];
      $this->weight =& $_SESSION['ClicShoppingCart']['total_weight'];
      $this->total =& $_SESSION['ClicShoppingCart']['total_cost'];
      $this->order_totals =& $_SESSION['ClicShoppingCart']['order_totals'];
    }

    /**
     * Remove All Items
     */
    public function removeAll()
    {
      $this->reset();
    }

    
    /**
     * @return bool
     */
    public function hasContents() :bool
    {
      return is_array($this->contents);
    }
    
    /*
     *  Restore the good qty in function B2BC / B2C or not and qty minimal or maximal defined
     *  string $qty
    */

    private function getRestoreQty(int $qty, string $products_id) :int
    {
      $qty = $this->getCheckGoodQty($products_id, $qty);

      return $qty;
    }

    /**
     * Restore the the cart content
     * @return false
     * @throws \Exception
     */
    public function getRestoreContents()
    {
      if (!$this->customer->isLoggedOn()) {
        return false;
      }

      $qty = 0;

// insert current cart contents in database
      if (is_array($this->contents)) {
        foreach ($this->contents as $item_id => $data) {
          $qty = $data['qty'];
          $this->productsId = $item_id;
          $restore_qty = $this->getRestoreQty($qty, $item_id);

          if ($qty < $restore_qty) $qty = $this->getRestoreQty();
          if ($qty > $restore_qty) $qty = $data['qty'];

          $Qcheck = $this->db->prepare('select products_id
                                         from :table_customers_basket
                                         where customers_id = :customers_id
                                         and products_id = :products_id'
                                      );

          $Qcheck->bindInt(':customers_id', $this->customer->getID());
          $Qcheck->bindValue(':products_id', $item_id);
          $Qcheck->execute();

          if ($Qcheck->fetch() === false) {
            $this->db->save('customers_basket', [
                'customers_id' => (int)$this->customer->getID(),
                'products_id' => $item_id,
                'customers_basket_quantity' => (int)$qty,
                'customers_basket_date_added' => date('Ymd')
              ]
            );

            if (isset($data['attributes'])) {
              foreach ($data['attributes'] as $option => $value) {
                $this->db->save('customers_basket_attributes', [
                    'customers_id' => (int)$this->customer->getID(),
                    'products_id' => $item_id,
                    'products_options_id' => (int)$option,
                    'products_options_value_id' => (int)$value
                  ]
                );
              }
            }
          } else {
            $this->db->save('customers_basket', ['customers_basket_quantity' => (int)$qty],
              ['customers_id' => (int)$this->customer->getID(),
               'products_id' => $item_id
              ]
            );
          }
        }
      }

// reset per-session cart contents, but not the database contents
      $this->reset(false);

      $_delete_array = [];

      if ($this->customer->getCustomersGroupID() != 0) {
        $Qproducts = $this->db->prepare('select cb.products_id as item_id,
                                              cb.customers_basket_quantity,
                                              cb.customers_basket_date_added,
                                              p.products_id as id, 
                                              p.parent_id,
                                              p.products_image,  
                                              p.products_price, 
                                              p.products_model, 
                                              p.products_tax_class_id, 
                                              p.products_weight, 
                                              p.products_weight_class_id, 
                                              p.products_status 
                                       from :table_customers_basket cb,
                                            :table_products p left join :table_products_groups g on p.products_id = g.products_id,
                                            :table_products_to_categories p2c,
                                            :table_categories c 
                                       where cb.customers_id = :customers_id
                                       and cb.products_id = p.products_id
                                       and p.products_archive = 0
                                       and g.products_group_view = 1
                                       and p.products_id = p2c.products_id
                                       and p2c.categories_id = c.categories_id
                                       and g.customers_group_id = :customers_group_id
                                       and c.status = 1
                                       order by cb.customers_basket_date_added desc 
                                      ');
        $Qproducts->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());
        $Qproducts->bindInt(':customers_id', $this->customer->getID());

        $Qproducts->execute();
      } else {
        $Qproducts = $this->db->prepare('select cb.products_id as item_id,
                                              cb.customers_basket_quantity,
                                              cb.customers_basket_date_added,
                                              p.products_id as id, 
                                              p.parent_id,
                                              p.products_image,  
                                              p.products_price, 
                                              p.products_model, 
                                              p.products_tax_class_id, 
                                              p.products_weight, 
                                              p.products_weight_class_id, 
                                              p.products_status 
                                       from :table_customers_basket cb,
                                              :table_products p,
                                              :table_products_to_categories p2c,
                                              :table_categories c 
                                       where cb.customers_id = :customers_id
                                       and cb.products_id = p.products_id
                                       and p.products_status = 1
                                       and p.products_archive = 0
                                       and p.products_id = p2c.products_id
                                       and p2c.categories_id = c.categories_id
                                       and c.status = 1
                                       order by cb.customers_basket_date_added desc 
                                      ');

        $Qproducts->bindInt(':customers_id', $this->customer->getID());

        $Qproducts->execute();
      }

      while ($Qproducts->fetch()) {
        $item_id = $Qproducts->value('item_id');

        if ( $Qproducts->valueInt('products_status') === 1 ) {
          $products_id = $Qproducts->valueInt('id');

          $Qdesc = $this->db->prepare('select products_name
                                      from :table_products_description 
                                      where products_id = :products_id
                                      and language_id = :language_id
                                      ');
          $Qdesc->bindInt(':products_id', $products_id);
          $Qdesc->bindInt(':language_id', $this->lang->getId());
          $Qdesc->execute();

          $products_price = $Qproducts->valueDecimal('products_price');

          if ($this->customer->getCustomersGroupID() != 0 && $Qproducts->valueInt('price_group_view') == 1) {
            $Qspecial = $this->db->prepare('select specials_new_products_price
                                            from :table_specials
                                            where products_id = :products_id
                                            and status = 1
                                            and customers_group_id = :customers_group_id
                                            ');

            $Qspecial->bindInt(':products_id', $products_id);
            $Qspecial->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

            $Qspecial->execute();
          } else {
            $Qspecial = $this->db->prepare('select specials_new_products_price
                                            from :table_specials
                                            where products_id = :products_id
                                            and status = 1
                                            ');

            $Qspecial->bindInt(':products_id', $products_id);

            $Qspecial->execute();
          }

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

          $this->contents[$Qproducts->valueInt('item_id')] = [
            'item_id' => $item_id,
            'qty' => $Qproducts->valueInt('customers_basket_quantity'),
            'id' => $products_id,
            'parent_id' => $Qproducts->valueInt('parent_id'),
            'model' => $Qproducts->value('products_model'),
            'name' => $Qdesc->value('products_name'),
            'image' => $Qproducts->value('products_image'),
            'price' => $products_price,
            'quantity' => $Qproducts->valueInt('quantity'),
            'weight' => $Qproducts->value('products_weight'),
            'tax_class_id' => $Qproducts->valueInt('products_tax_class_id'),
            // date_added' => DateTime::getShort($Qproducts->value('date_added')),
            'products_weight_class_id' => $Qproducts->valueInt('products_weight_class_id')
            ];

// attributes
          $Qattributes = $this->db->prepare('select products_options_id,
                                                    products_options_value_id
                                              from :table_customers_basket_attributes
                                              where customers_id = :customers_id
                                              and products_id = :products_id
                                              ');

          $Qattributes->bindInt(':customers_id', $this->customer->getID());
          $Qattributes->bindValue(':products_id', $Qproducts->value('item_id'));
          $Qattributes->execute();

          while ($Qattributes->fetch()) {
            $this->contents[$Qproducts->value('item_id')]['attributes'][$Qattributes->valueInt('products_options_id')] = $Qattributes->valueInt('products_options_value_id');
          }
        } else {
          $_delete_array[] = $item_id;
        }
      }

      if (!empty($_delete_array)) {
        foreach ($_delete_array as $id) {
          unset($this->contents[$id]);
        }

        $Qdelete = $this->db->prepare('delete 
                                       from :table_customers_basket
                                       where customers_id = :customers_id 
                                       and products_id in ("' . implode('", "', $_delete_array) . '")'
                                     );
        $Qdelete->bindInt(':customers_id', $this->customer->getID());
        $Qdelete->execute();
      }
      
      $this->cleanUp();
      $this->calculate();
// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    /**
     * remove all items
     * @param bool $reset_database
     */
    public function reset(bool $reset_database = false)
    {
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
    public function add(string $products_id, int $qty = 1, $attributes = '', bool $notify = true)
    {
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
            $check = $this->productsAttributes->getCheckProductsAttributes($products_id, $option, $value);

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
        $Qcheck = $this->db->prepare('select p.products_id
                                      from :table_products p,
                                           :table_products_to_categories p2c,
                                           :table_categories c
                                      where p.products_id = :products_id
                                      and p.products_status = 1
                                      and p.products_archive = 0
                                      and p.products_id = p2c.products_id
                                      and p2c.categories_id = c.categories_id
                                      and c.status = 1
                                    ');

        $Qcheck->bindInt(':products_id', $products_id);
        $Qcheck->execute();

        if ($Qcheck->fetch() !== false) {
          if ($notify === true) {
            $_SESSION['new_products_id_inCart'] = $products_id;
          }

          if ($this->inCart($products_id_string)) {
            $this->updateQuantity($products_id_string, $qty, $attributes);
          } else {
            $this->contents[$products_id_string] = ['qty' => (int)$qty];

// insert into database
            if ($this->customer->isLoggedOn()) {

              $this->db->save('customers_basket', [
                'customers_id' => (int)$this->customer->getID(),
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

                  $this->db->save('customers_basket_attributes', [
                    'customers_id' => (int)$this->customer->getID(),
                    'products_id' => $products_id_string,
                    'products_options_id' => (int)$option,
                    'products_options_value_id' => (int)$value
                    ]
                  );
                }
              }
            }
          }

          $this->cleanUp();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
          $this->cartID = $this->generate_cart_id();
        }
      }
    }

    /**
     * @param string $products_id
     * @param int $qty
     * @param array $attributes
     * @param bool $notify
     * @throws \Exception
     */
    public function addCart(string $products_id,int $qty = 1, $attributes = '', bool $notify = true)
    {
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
            $check = $this->productsAttributes->getCheckProductsAttributes($products_id, $option, $value);

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
        $Qcheck = $this->db->prepare('select p.products_id
                                      from :table_products p,
                                           :table_products_to_categories p2c,
                                           :table_categories c
                                      where p.products_id = :products_id
                                      and p.products_status = 1
                                      and p.products_archive = 0
                                      and p.products_id = p2c.products_id
                                      and p2c.categories_id = c.categories_id
                                      and c.status = 1
                                    ');

        $Qcheck->bindInt(':products_id', $products_id);
        $Qcheck->execute();

        if ($Qcheck->fetch() !== false) {
          if ($notify === true) {
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

          $this->cleanUp();

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
     * @param $products_id
     * @param string $quantity
     * @param array $attributes
     * @throws \Exception
     */
    public function updateQuantity(string $products_id, int $quantity, $attributes = '')
    {
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

      if (is_numeric($products_id) && isset($this->contents[$products_id_string]) && is_numeric($quantity) && ($attributes_pass_check === true)) {
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
     * items cleanUp
     */
    private function cleanUp()
    {
      foreach ($this->contents as $item_id => $data) {
        if ( $data['qty'] < 1 ) {
          unset($this->contents[$item_id]);
// remove from database
          if ($this->customer->isLoggedOn()) {
            $array = [
              'customers_id' => $this->customer->getID(),
              'products_id' => $item_id
            ];

            $this->db->delete('customers_basket', $array);
            $this->db->delete('customers_basket_attributes', $array);
          }
        }
      }
    }

    /**
     * @param $product_id
     * @return int|string
     */
    public function getBasketID($product_id)
    {
      foreach ( $this->contents as $item_id => $product ) {
        if ( $product['id'] == $product_id ) {
          return $item_id;
        }
      }
    }
    
    /**
     * get total number of items in cart
     * @return int
     */
    public function getCountContents() :int
    {
      $total_items = 0;

      if (is_array($this->contents)) {
        foreach ($this->contents as $item_id => $data) {
          $total_items += $this->getQuantity($item_id);
        }
      }

      return $total_items;
    }


    /**
     * get total quantity on each items in cart
     * @param string $item_id
     * @return int
     */
    public function getQuantity(string $item_id)
    {
      return ( isset($this->contents[$item_id]) ) ? $this->contents[$item_id]['qty'] : 0;
    }

    /*
     * get total quantity on each items in cart
     * @param : int $products_id, id of the product
     * @return : int qty
     */
    public function inCart(string $products_id) :bool
    {
      if (isset($this->contents[$products_id])) {
        return true;
      } else {
        return false;
      }
    }

    /**
     * @param string $item_id
     * @throws \Exception
     */
    public function remove(string $item_id)
    {
      unset($this->contents[$item_id]);

// remove from database
      if ($this->customer->isLoggedOn()) {

        $this->db->delete('customers_basket', [
          'customers_id' => (int)$this->customer->getID(),
          'products_id' => $item_id
          ]
        );

        $this->db->delete('customers_basket_attributes', [
          'customers_id' => (int)$this->customer->getID(),
          'products_id' => $item_id
          ]
        );
      }

      $this->calculate();

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    /**
     * @return false|string
     */
    public function getProductIdList()
    {
      $product_id_list = '';

      if (is_array($this->contents)) {
        foreach ($this->contents as $item_id => $data) {
          $product_id_list .= ', ' . $item_id;
        }
      }

      return substr($product_id_list, 2);
    }

    /**
     * @throws \Exception
     */
    private function calculate()
    {
      $CLICSHOPPING_Weight = Registry::get('Weight');

      $this->sub_total = 0;
      $this->total = 0;
      $this->weight = 0;
      $this->order_totals = [];

      $_SESSION['cartID'] = $this->generate_cart_id();

      if ($this->hasContents()) {
        foreach ($this->contents as $item_id => $data) {
          $qty = $data['qty'];

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
                                            from :table_products p left join :table_products_groups g on p.products_id = g.products_id,
                                                 :table_products_to_categories p2c,
                                                 :table_categories c
                                            where p.products_id = :products_id
                                            and g.customers_group_id = :customers_group_id
                                            and g.products_group_view = 1
                                            and p.products_id = p2c.products_id
                                            and p2c.categories_id = c.categories_id
                                            and c.status = 1
                                            and p.products_archive = 0
                                           ');

            $Qproduct->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());
            $Qproduct->bindInt(':products_id', $item_id);
            $Qproduct->execute();
          } else {
            $Qproduct = $this->db->prepare('select p.products_id,
                                                   p.products_price,
                                                   p.products_tax_class_id,
                                                   p.products_weight,
                                                   p.products_weight_class_id,
                                                   p.products_dimension_width,
                                                   p.products_dimension_height,
                                                   p.products_dimension_depth
                                            from :table_products p,
                                                 :table_products_to_categories p2c,
                                                 :table_categories c
                                            where p.products_id = :products_id
                                            and p.products_id = p2c.products_id
                                            and p2c.categories_id = c.categories_id
                                            and c.status = 1
                                            and p.products_archive = 0
                                            and p.products_status = 1
                                           ');

            $Qproduct->bindInt(':products_id', $item_id);
            $Qproduct->execute();
          }

          if ($Qproduct->fetch() !== false) {
            $prid = $Qproduct->valueInt('products_id');
            $products_tax = $this->tax->getTaxRate($Qproduct->valueInt('products_tax_class_id'));
            $products_price = $Qproduct->valueDecimal('products_price');

            $products_weight = $CLICSHOPPING_Weight->convert($Qproduct->valueDecimal('products_weight'), $Qproduct->valueInt('products_weight_class_id'), SHIPPING_WEIGHT_UNIT);
            $this->weight += ($qty * $products_weight);

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

              $Qspecial->bindInt(':products_id', $prid);
              $Qspecial->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

              $Qspecial->execute();
            } else {
              $Qspecial = $this->db->prepare('select specials_new_products_price
                                              from :table_specials
                                              where products_id = :products_id
                                              and status = 1
                                             ');

              $Qspecial->bindInt(':products_id', $prid);

              $Qspecial->execute();
            }

            if ($Qspecial->fetch() !== false) {
              $products_price = $Qspecial->valueDecimal('specials_new_products_price');
            }

            $min_quantity = $this->productsCommon->getProductsMinimumQuantity($item_id);

  // Total calculation
            if ($qty < (int)$min_quantity) $qty = (int)$min_quantity;
  // product discount on quantity
            $new_price_with_discount_quantity = $this->productsCommon->getProductsNewPriceByDiscountByQuantity($item_id, $qty, $products_price);

            if ($new_price_with_discount_quantity > 0) {
              $products_price = $new_price_with_discount_quantity;
            }

// Probleme avec calculate price qui ne converti pas correctement les informations voir la classe shopping_cart
            $this->total += $this->tax->addTax($products_price, $products_tax) * $qty;
          }

// attributes price
          if (isset($data['attributes'])) {
            foreach ($data['attributes'] as $option => $value) {
              $Qattributes = $this->db->prepare('select options_values_price,
                                                        price_prefix
                                                  from :table_products_attributes
                                                  where products_id = :products_id
                                                  and options_id = :options_id
                                                  and options_values_id = :options_values_id
                                                  and status = 1
                                                ');

              $Qattributes->bindInt(':products_id', $prid);
              $Qattributes->bindInt(':options_id', $option);
              $Qattributes->bindInt(':options_values_id', $value);
              $Qattributes->execute();

              if ($Qattributes->fetch() !== false) {
  // La prix et l'attribut ((additionne) sont en rapport avec la quantite - total dela commande
                if ($Qattributes->value('price_prefix') == '+') {
                  $this->total += $qty * $this->tax->addTax($Qattributes->valueDecimal('options_values_price'), $products_tax);
                }

  // La prix et l'attribut (soustraction) sont en rapport avec la quantite - total dela commande
                if ($Qattributes->value('price_prefix') == '-') {
                  $this->total -= $qty * $this->tax->addTax($Qattributes->valueDecimal('options_values_price'), $products_tax);
                }
              }
            }
          }
        }
      }
    }

    /**
     * get_products
     * @return array|null
     */
    public function get_products() :?array
    {
      if ($this->hasContents()) {
        $products_array = [];

        foreach ($this->contents as $item_id => $data) {
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
                                                   p.products_length_class_id,
                                                   p.products_tax_class_id,
                                                   g.price_group_view,
                                                   g.customers_group_price
                                            from :table_products p left join :table_products_groups g on p.products_id = g.products_id,
                                                 :table_products_description pd,
                                                 :table_products_to_categories p2c,
                                                 :table_categories c
                                            where p.products_id = :products_id
                                            and pd.products_id = p.products_id
                                            and g.customers_group_id = :customers_group_id
                                            and g.products_group_view = 1
                                            and pd.language_id = :language_id
                                            and p.products_id = p2c.products_id
                                            and p2c.categories_id = c.categories_id
                                            and c.status = 1
                                            and p.products_archive = 0
                                      ');

            $Qproducts->bindInt(':products_id', $item_id);
            $Qproducts->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());
            $Qproducts->bindInt(':language_id', $this->lang->getId());
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
                                                   p.products_length_class_id,
                                                   p.products_tax_class_id
                                            from :table_products p,
                                                 :table_products_description pd,
                                                 :table_products_to_categories p2c,
                                                 :table_categories c
                                            where p.products_id = :products_id
                                            and pd.products_id = p.products_id
                                            and pd.language_id = :language_id
                                            and p.products_id = p2c.products_id
                                            and p2c.categories_id = c.categories_id
                                            and c.status = 1
                                            and p.products_archive = 0
                                            and p.products_status = 1
                                         ');
            $Qproducts->bindInt(':products_id', $item_id);
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
              $Qspecial->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());
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
            $new_price_with_discount_quantity = $this->productsCommon->getProductsNewPriceByDiscountByQuantity($item_id, $data['qty'], $products_price);

            if ($new_price_with_discount_quantity > 0) {
              $products_price = $new_price_with_discount_quantity;
            }

            if ($Qproducts->value('products_model_group') != '' && $this->customer->getCustomersGroupID() != 0) {
              $model = $Qproducts->value('products_model_group');
            } else {
              $model = $Qproducts->value('products_model');
            }

            $attributes_price = $this->getAttributesPrice($item_id);

            $finale_price = $products_price + $attributes_price;

            $products_array[] = ['id' => $item_id,
              'name' => $Qproducts->value('products_name'),
              'model' => $model,
              'image' => $Qproducts->value('products_image'),
              'price' => $products_price,
              'quantity' => (int)$data['qty'],
              'weight' => $Qproducts->valueDecimal('products_weight'),
              'products_weight_class_id' => (int)$Qproducts->valueint('products_weight_class_id'),
              'products_dimension_width' => $Qproducts->valueDecimal('products_dimension_width'),
              'products_dimension_height' => $Qproducts->valueDecimal('products_dimension_height'),
              'products_dimension_depth' => $Qproducts->valueDecimal('products_dimension_depth'),
              'final_price' => $finale_price,
              'tax_class_id' => (int)$Qproducts->valueInt('products_tax_class_id'),
              'attributes' => (isset($data['attributes']) ? $data['attributes'] : '')
            ];
          }
        }
      }

      return $products_array;
    }

    /**
     * @return mixed
     */
    public function getSubTotal() :float
    {
      return $this->sub_total;
    }

    /**
     * @return float
     * @throws \Exception
     */
    public function show_total() :float
    {
      $this->calculate();

      return $this->total;
    }

    /**
     * @return float
     * @throws \Exception
     */
   public function getWeight() :float
    {
      $this->calculate();

      return $this->weight;
    }

    /**
     * @param int $length
     * @return bool|string
     * @throws \Exception
     */
    public function generate_cart_id(int $length = 5)
    {
      return Hash::getRandomString($length, 'digits');
    }

    /**
     * @return bool|string
     */
    public function get_content_type()
    {
      $this->content_type = false;

      if ((DOWNLOAD_ENABLED == 'true') && ($this->getCountContents() > 0)) {
        foreach ($this->contents as $item_id => $data) {
          if (isset($data['attributes'])) {
            foreach ($data['attributes'] as $value) {
              $check = $this->productsAttributes->getCheckProductsDownload($item_id, $value);

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

    /**
     * @param string $item_id
     * @return bool
     */
    public function isInStock(string $item_id) :bool
    {
      $Qstock = $this->db->prepare('select products_quantity 
                                    from :table_products 
                                    where products_id = :products_id
                                   ');
      $Qstock->bindInt(':products_id', $this->contents[$item_id]['id']);
      $Qstock->execute();

      if (($Qstock->valueInt('products_quantity') - $this->contents[$item_id]['quantity']) >= 0) {
        return true;
      } elseif ( $this->products_in_stock === true ) {
        $this->products_in_stock = false;
      }

      return false;
    }

    /**
     * @return mixed
     */
    public function hasStock()
    {
      return $this->products_in_stock;
    }

    /**
     * @param $broken
     */
    public function unserialize(array $broken)
    {
      foreach ($broken as $k => $v) {
        $kv = [$k, $v];
        $key = $kv['key'];
        if (gettype($this->$key) != 'user function')
          $this->$key = $kv['value'];
      }
    }

    /**
     * Return a product ID with attributes
     * @param string $prid , $params
     * @return string $uprid,
     */
    public function getUprid($prid, $params)
    {
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
            $attributes = explode('{', substr($prid, strpos($prid, '{') + 1));

            for ($i = 0, $n = count($attributes); $i < $n; $i++) {
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
     * @param $uprid
     * @return false|int
     */
    public function getPrid($uprid)
    {
      $pieces = explode('{', $uprid);

      if (is_numeric($pieces[0])) {
        return (int)$pieces[0];
      } else {
        return false;
      }
    }

    /**
     * @param string $products_id
     * @param int $qty
     * @return int
     */
    public function getCheckGoodQty(string $products_id, int $qty) :int
    {
      if (defined('MAX_QTY_IN_CART') && (MAX_QTY_IN_CART > 0) && ((int)$qty > MAX_QTY_IN_CART)) {
        $qty = (int)MAX_QTY_IN_CART;
      }

      if (($this->getProductsMinOrderQtyShoppingCart($products_id) > 1) && ((int)$qty < $this->getProductsMinOrderQtyShoppingCart($products_id))) {
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
     * @param int $products_id ; id of the product
     * @return $min_order_qty_values, nimum order quantity
     *
     */
    public function getProductsMinOrderQtyShoppingCart(string $products_id) :int
    {
      $products_id = $this->prod->getProductID($products_id);

      if ($this->customer->getCustomersGroupID() == 0) {
        $QminOrderQty = $this->db->prepare('select p.products_min_qty_order
                                            from :table_products p,
                                                 :table_products_to_categories p2c,
                                                 :table_categories c
                                            where p.products_id = :products_id
                                            and p.products_id = p2c.products_id
                                            and p2c.categories_id = c.categories_id
                                            and c.status = 1
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

    /**
     * get the attributes price
     * @param string $products_id , the id of the products
     * @return $attributes_price the price of the attributes
     *
     */
    public function getAttributesPrice(string $products_id) :float
    {
      $attributes_price = 0;

      if (isset($this->contents[$products_id]['attributes']) && is_array($this->contents[$products_id]['attributes'])) {
        foreach ($this->contents[$products_id]['attributes'] as $option => $value) {
          $Qattributes = $this->db->prepare('select options_values_price,
                                                    price_prefix
                                              from :table_products_attributes
                                              where products_id = :products_id
                                              and options_id = :options_id
                                              and options_values_id = :options_values_id
                                              and status = 1
                                             ');
          $Qattributes->bindValue(':products_id', $products_id);
          $Qattributes->bindInt(':options_id', $option);
          $Qattributes->bindInt(':options_values_id', $value);

          $Qattributes->execute();

          if ($Qattributes->fetch() !== false) {
            if ($Qattributes->value('price_prefix') == '+') {
              $attributes_price += $Qattributes->valueDecimal('options_values_price');
            } else {
              $attributes_price -= $Qattributes->valueDecimal('options_values_price');
            }
          }
        }
      }

      return $attributes_price;
    }
  }