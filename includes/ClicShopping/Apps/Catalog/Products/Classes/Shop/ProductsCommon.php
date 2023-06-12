<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Apps\Catalog\Products\Classes\Shop;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\DateTime;
  use ClicShopping\OM\CLICSHOPPING;

  class ProductsCommon extends Prod
  {
    protected $id;
    protected $products_name;
    protected $products_short_description;
    protected $products_stock;
    protected $image_new_arrival;
    protected $products_flash_discount;
    protected $product_price;
    protected $products_quantity_unit;
    protected $min_order_quantity_products_display;
    protected $input_quantity;
    protected $button_small_view_details;
    protected $submit_button;
    protected $products_image;
    protected $products_model;
    protected $products_manufacturers;
    protected $product_price_kilo;
    protected $products_date_available;
    protected $products_only_shop;
    protected $products_only_web;
    protected $products_packaging;
    protected $products_shipping_delay;
    protected $products_shipping_delay_out_of_stock;
    protected $products_tag;
    protected $products_volume;
    protected $products_weight;
    protected $buy_button;
    protected $delete_word;
    protected $size_button;
    protected $products_dimension;
    protected $submit_button_view;
    protected $manufacturers_id;
    protected $products_weight_class_id;
    protected $infoPriceDiscountByQuantity;
    protected $saveMoney;

    protected mixed $app;
    protected mixed $db;
    protected $language;
    protected $customer;

    public function __construct()
    {
      $this->db = Registry::get('Db');
      $this->customer = Registry::get('Customer');
      $this->language = Registry::get('Language');
    }

    /**
     * get product id if exist
     * @return bool|int|null
     */
    public function getID()
    {
      if (parent::getID() === null || !is_numeric(parent::getID()) || empty(parent::getID())) {
        return false;
      } else {
        $id = parent::getID();
      }

      return $id;
    }

    /**
     * @return bool or array
     */
    private function setData()
    {
      if ($this->customer->getCustomersGroupID() != 0) {
        $Qproducts = $this->db->prepare('select p.products_id,
                                                p.products_tax_class_id,
                                                p.orders_view,
                                                p.products_view,
                                                p.products_archive,
                                                p.products_quantity,
                                                g.customers_group_price,
                                                g.price_group_view,
                                                g.orders_group_view,
                                                g.products_group_view
                                        from :table_products p left join :table_products_groups g on p.products_id = g.products_id
                                        where p.products_status = 1
                                        and g.customers_group_id = :customers_group_id
                                        and g.products_group_view = 1
                                        and p.products_id = :products_id
                                       ');

        $Qproducts->bindInt(':products_id', $this->getID());
        $Qproducts->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());
        $Qproducts->execute();
        $result = $Qproducts->toArray();
      } else {
        $Qproducts = $this->db->prepare('select p.products_id,
                                                p.products_tax_class_id,
                                                p.products_quantity,
                                                p.orders_view,
                                                p.products_view,
                                                p.products_archive,
                                                p.products_tax_class_id
                                        from :table_products p,
                                             :table_products_to_categories p2c,
                                             :table_categories c
                                        where p.products_status = 1
                                        and p.products_view = 1
                                        and p.products_id = :products_id
                                        and p.products_id = p2c.products_id
                                        and p2c.categories_id = c.categories_id
                                        and c.status = 1
                                       ');

        $Qproducts->bindInt(':products_id', $this->getID());
        $Qproducts->execute();
        $result = $Qproducts->toArray();
      }

      if ($result === false) return false;

      return $result;
    }

    public function getData()
    {
      return $this->setData();
    }

    /**
     * returns a single element of the data array
     * @param null $obj
     * @return bool
     */
    public function get($obj = null)
    {
      $array_data = $this->getData();

      if (isset($array_data[$obj])) {
        return $array_data[$obj];
      }

      return $array_data;
    }

    public function getProductsGroupView()
    {
      return $this->get('products_group_view');
    }

    public function getProductsView()
    {
      return $this->get('products_view');
    }

    public function getProductsArchive()
    {
      return $this->get('products_archive');
    }

    public function getProductsQuantity()
    {
      return $this->get('products_quantity');
    }

    public function getProductsTaxClassId()
    {
      return $this->get('products_tax_class_id');
    }

    public function getOrdersGroupView()
    {
      return $this->get('orders_group_view');
    }

    public function getProductsOrdersView()
    {
      return $this->get('orders_view');
    }

    public function getPriceGroupView()
    {
      return $this->get('price_group_view');
    }

    private function getCustomersGroupPrice()
    {
      return $this->get('customers_group_price');
    }

    /**
     * Check if the product  id
     * @param int $id , id of the product
     */
    public function checkID($id)
    {
      $CLICSHOPPING_Session = Registry::get('Session');

      $result = (preg_match('/^[0-9]+(#?([0-9]+:?[0-9]+)+(;?([0-9]+:?[0-9]+)+)*)*$/', $id) || preg_match('/^[a-zA-Z0-9 -_]*$/', $id)) && ($id != $CLICSHOPPING_Session->getName());

      return $result;
    }

    /**
     * Check if the product  exist
     * @param int $id , id of the product
     * @return string $result
     */
    public function checkEntry($id)
    {
      if ($this->checkID($id) === false) {
        return false;
      } else {

        if ($this->customer->getCustomersGroupID() != 0) {
          $sql_query = 'select p.products_id
                     from :table_products p left join :table_products_groups g on p.products_id = g.products_id';

          if (is_numeric($id)) {
            $sql_query .= ' where p.products_id = :products_id';
          } else {
            $sql_query .= ', :table_products_description pd
                          where pd.products_keyword = :products_keyword
                          and pd.products_id = p.products_id';
          }

          $sql_query .= ' and p.products_status = 1
                        and p.products_archive = 0
                        and g.customers_group_id = ' . $this->customer->getCustomersGroupID() . '
                        and g.products_group_view = 1
                        limit 1
                    ';
        } else {
          $sql_query = 'select p.products_id from :table_products p';

          if (is_numeric($id)) {
            $sql_query .= ' where p.products_id = :products_id';
          } else {
            $sql_query .= ', :table_products_description pd
                          where pd.products_keyword = :products_keyword
                          and pd.products_id = p.products_id';
          }

          $sql_query .= ' and p.products_status = 1
                         and p.products_archive = 0
                         and p.products_view = 1
                         limit 1
                      ';
        }

        $Qproduct = $this->db->prepare($sql_query);

        if (is_numeric($id)) {
          $Qproduct->bindInt(':products_id', $id);
        } else {
          $Qproduct->bindValue(':products_keyword', $id);
        }

        $Qproduct->execute();

        $result = $Qproduct->fetch();
      }

      return (($result !== false) && (count($result) === 1));
    }

    /**
     * Update the the number of products view
     * @return update product views
     */
    public function countUpdateProductsView()
    {
      $Qupdate = $this->db->prepare('update :table_products_description
                                      set products_viewed = products_viewed+1
                                      where products_id = :products_id
                                      and language_id =:language_id
                                     ');
      $Qupdate->bindInt(':products_id', $this->getID());
      $Qupdate->bindInt(':language_id', $this->language->getId());

      return $Qupdate->execute();
    }

    /**
     * Number of products
     * @return float $product_check['total'], products total
     */
    public function getProductsCount() :float
    {
      $QproductCheck = $this->db->prepare('select count(*) as total
                                          from :table_products p,
                                               :table_products_description pd
                                          where p.products_status = 1
                                          and p.products_id = :products_id
                                          and pd.products_id = p.products_id
                                          and pd.language_id = :language_id
                                         ');

      $QproductCheck->bindInt(':products_id', $this->getID());
      $QproductCheck->bindInt(':language_id', $this->language->getId());

      $QproductCheck->execute();

      return $QproductCheck->valueInt('total');
    }

    /**
     * Display products name
     * @param int|null $id
     * @return string $products_name, name of the product
     *
     */
    public function getProductsName(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qproducts = $this->db->prepare('select pd.products_name
                                      from :table_products p,
                                           :table_products_description pd
                                      where p.products_status = 1
                                      and p.products_id = :products_id
                                      and pd.products_id = p.products_id
                                      and pd.language_id = :language_id
                                     ');

      $Qproducts->bindInt(':products_id', $id);
      $Qproducts->bindInt(':language_id', $this->language->getId());

      $Qproducts->execute();

      $products_name = HTML::output($Qproducts->value('products_name'));

      return $products_name;
    }

    /**
     * products image
     * @param int|null $id
     * @return string $products_image, image of the product
     * @access private
     */
    private function setProductsImage(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_image'], $array);

      $products_image = HTML::outputProtected($Qproducts->value('products_image'));

      return $products_image;
    }

    /**
     * display products image
     * @param int|null $id
     * @return string $products_image, image of the product
     *
     */
    public function getProductsImage(?int $id = null)
    {
      if (\is_null($this->setProductsImage($id))) {
        return false;
      } else {
        return $this->setProductsImage($id);
      }
    }

    /**
     * products image medium
     * @param int|null $id
     * @return string $products_image_medium, image medium of the product
     * @access private
     */
    private function setProductsImageMedium(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_image_medium'], $array);

      $products_image_medium = HTML::outputProtected($Qproducts->value('products_image_medium'));

      return $products_image_medium;
    }

    /**
     * display products image medium
     * @param int|null $id
     * @return string $products_image_medium, image medium of the product
     */
    public function getProductsImageMedium(?int $id = null)
    {
      if (\is_null($this->setProductsImageMedium($id))) {
        return false;
      } else {
        return $this->setProductsImageMedium($id);
      }
    }

    /**
     * display date available
     * @param int|null $id
     * @return string
     */
    public function getProductsDateAvailable(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_date_available'], $array);

      $products_date_available = HTML::outputProtected($Qproducts->value('products_date_available'));

      return $products_date_available;
    }

    /**
     * display products ean
     * @param string
     * @return string
     */
    public function getProductsEAN() :string
    {
      $array = [
        'products_status' => 1,
        'products_id' => (int)$this->getID()
      ];

      $Qproducts = $this->db->get('products', ['products_ean'], $array);

      $products_ean = HTML::outputProtected($Qproducts->value('products_ean'));

      return $products_ean;
    }

    /**
     * display products sku
     * @param int|null $id
     * @return string
     */
    public function getProductsSKU(?int $id = null): string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_sku'], $array);

      $products_sku = HTML::outputProtected($Qproducts->value('products_sku'));

      return $products_sku;
    }

    /**
     * display products jan
     * @param int|null $id
     * @return string $products_name, name of the product
     */
    public function getProductsJAN(?int $id = null): string
    {
      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_jan'], $array);

      $products_jan= HTML::outputProtected($Qproducts->value('products_jan'));

      return $products_jan;
    }

    /**
     * display products isbn
     * @param int|null $id
     * @return string
     */
    public function getProductsISBN(?int $id = null): string
    {
      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_isbn'], $array);

      $products_isbn = HTML::outputProtected($Qproducts->value('products_isbn'));

      return $products_isbn;
    }

    /**
     * display products mnp
     * @param int|null $id
     * @return string
     */
    public function getProductsMNP(?int $id = null): string
    {
      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_mpn'], $array );

      $products_mpn = HTML::outputProtected($Qproducts->value('products_mpn'));

      return $products_mpn;
    }

    /**
     * display products upc
     * @param int|null $id
     * @return string
     */
    public function getProductsUPC(?int $id = null): string
    {
      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_upc'], $array);

      $products_upc = HTML::outputProtected($Qproducts->value('products_upc'));

      return $products_upc;
    }


    /**
     * display products barcode
     * @param string
     * @return string bar code
     */
    public function getProductsBarCode() :string
    {
      $array = [
        'products_status' => 1,
        'products_id' => (int)$this->getID()
      ];

      $Qproducts = $this->db->get('products', ['products_barcode'], $array);

      $products_barcode = HTML::outputProtected($Qproducts->value('products_barcode'));

      return $products_barcode;
    }

    /**
     * display products description
     * @param int|null $id
     * @return string $products_description, description of the product
     *
     */
    public function getProductsDescription(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qproducts = $this->db->prepare('select pd.products_description
                                        from :table_products p,
                                             :table_products_description pd
                                        where p.products_status = 1
                                        and p.products_id = :products_id
                                        and pd.products_id = p.products_id
                                        and pd.language_id = :language_id
                                       ');

      $Qproducts->bindInt(':products_id', $id);
      $Qproducts->bindInt(':language_id', $this->language->getId());

      $Qproducts->execute();

      return $Qproducts->value('products_description');
    }

    /**
     * Display Short Description
     * @param int|null $id
     * @param int $delete_word , number of the words to delete
     * @param int $products_short_description_number
     * @return string $short_description , short description
     */
    public function getProductsShortDescription(?int $id = null, int $delete_word = 0, int $products_short_description_number = 0) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $delete_word = HTML::sanitize($delete_word);
      $products_short_description_number = HTML::sanitize($products_short_description_number);

      $Qproducts = $this->db->prepare('select pd.products_description_summary
                                        from :table_products p,
                                             :table_products_description pd
                                        where p.products_status = 1
                                        and p.products_id = :products_id
                                        and pd.products_id = p.products_id
                                        and pd.language_id = :language_id
                                       ');

      $Qproducts->bindInt(':products_id', $id);
      $Qproducts->bindInt(':language_id', $this->language->getId());

      $Qproducts->execute();

      $description_summary = $Qproducts->value('products_description_summary');

      if ($products_short_description_number > 0) {
        $short_description = substr($description_summary, (int)$delete_word, (int)$products_short_description_number);
        $description_summary = HTML::breakString(HTML::outputProtected($short_description), $products_short_description_number, '-<br />') . ((\strlen($description_summary) >= $products_short_description_number - 1) ? ' ...' : '');
      } else {
        $description_summary = '';
      }

      return $description_summary;
    }

    /**
     * display products dimension
     * @param int|null $id
     * @param string
     * @return string $products_dimension, dimension of the product
     */
    public function getProductsDimension(?int $id = null, string $separator = ' x ') :string
    {
      $CLICSHOPPING_ProductsLength = Registry::get('ProductsLength');

      if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qproducts = $this->db->get('products', [
        'products_dimension_width',
        'products_dimension_height',
        'products_dimension_depth',
        'products_weight_class_id'
      ],
        ['products_status' => 1,
          'products_id' => (int)$id
        ]
      );

      $products_length_class_id = $Qproducts->valueInt('products_weight_class_id');
      $products_dimension_width = $Qproducts->valueDecimal('products_dimension_width');
      $products_dimension_height = $Qproducts->valueDecimal('products_dimension_height');
      $products_dimension_depth = $Qproducts->valueDecimal('products_dimension_depth');

      $products_type = $CLICSHOPPING_ProductsLength->getUnit($products_length_class_id, $this->language->getId());

      if (($products_dimension_width == 0 || empty($products_dimension_width)) ||
        ($products_dimension_height == 0 || empty($products_dimension_height)) ||
        ($products_dimension_depth == 0 || empty($products_dimension_depth))
      ) {
        return false;
      } else {
        $products_dimension = HTML::outputProtected($products_dimension_width . $separator . $products_dimension_height  . $separator . $products_dimension_depth . ' ' . $products_type);
      }

      return $products_dimension;
    }

    /**
     * display products manufacturer
     * @param int|null $id
     * @return string $products_manufacturer, manufacturer of the product
     */
    public function getProductsManufacturer(?int $id = null)
    {
      $manufacturer_search = '';

      if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qproducts = $this->db->prepare('select manufacturers_id
                                        from :table_products
                                        where  products_id = :products_id
                                        limit 1
                                       ');

      $Qproducts->bindInt(':products_id', $id);
      $Qproducts->execute();

      $Qmanufacturer = $this->db->prepare('select m.manufacturers_name
                                          from :table_manufacturers m,
                                               :table_products p
                                          where m.manufacturers_id = :manufacturers_id
                                          and p.products_id = :products_id
                                        ');
      $Qmanufacturer->bindInt(':manufacturers_id', $Qproducts->valueInt('manufacturers_id'));
      $Qmanufacturer->bindInt(':products_id', $id);

      $Qmanufacturer->execute();

      if ($Qmanufacturer->fetch()) {
        $manufacturer_search = $Qmanufacturer->value('manufacturers_name');
      }

      return $manufacturer_search;
    }

    /**
     * display the button in function the boostrap size
     * @param string $size_button , size could be sm, md ...
     */
    public function getSizeButton($size_button)
    {
      $size_button = HTML::sanitize($size_button);

      return $size_button;
    }

    /**
     * display image ou button type products new arrival (news)
     * @param int|null $id
     * @return string $icon_new_arrival_products, product new arrival
     */
    public function getProductsNewArrival(?int $id = null, $size_button = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $size_button = $this->getSizeButton($size_button);

      $Qproducts = $this->db->get('products', ['products_date_added'],
        ['products_status' => 1,
          'products_id' => (int)$id
        ]
      );

      $products_date_added = $Qproducts->value('products_date_added');

      if (!empty($size_button)) {
        $size_button = $this->getSizeButton($size_button);
      }

//  2592000 = 30 days in the unix timestamp format
      $day_new_products = 86400 * (int)DAY_NEW_PRODUCTS_ARRIVAL;
      $today_time = time();

      if (($today_time - strtotime($products_date_added)) < $day_new_products) {
        $product_button_new_arrival = HTML::button(CLICSHOPPING::getDef('button_new_product'), null, null, 'new', null, $size_button);

        $icon_new_arrival_products = '&nbsp' . $product_button_new_arrival;

        return $icon_new_arrival_products;
      } else {
        return '';
      }
    }

    /**
     * display sell only the shop
     * @param int|null $id
     * @return string $products_only_shop, sell only the shop
     */
    public function getProductsOnlyTheShop(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_only_shop'], $array);

      $products_only_shop = $Qproducts->value('products_only_shop');

      return $products_only_shop;
    }

    /**
     * display sell only the shop
     * @param int|null $id
     * @return string $products_only_online, products only on the web site
     */
    public function getProductsOnlyOnTheWebSite(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_only_online'], $array);

      $products_only_online = $Qproducts->value('products_only_online');

      return $products_only_online;
    }

    /**
     * display products packaging
     * @param null|int
     * @return string $products_packaging, products packaging
     */
    public function getProductsPackaging(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_packaging'], $array);

      $products_packaging = HTML::outputProtected($Qproducts->value('products_packaging'));

      return $products_packaging;
    }

    /**
     * display products  Date Added
     * @param int|null $id
     * @return string $products_packaging, products packaging
     */
    public function getProductsDateAdded(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qproducts = $this->db->prepare('select products_date_added
                                       from :table_products
                                       where products_status = 1
                                       and products_id = :products_id
                                       order by products_last_modified desc
                                       limit 1
                                     ');
      $Qproducts->bindInt(':products_id', $id);

      $Qproducts->execute();

      return $Qproducts->value('products_date_added');
    }

    /**
     * display products quantity unit type
     * @param int|null $id
     * @return string $products_quantity_unit['products_quantity_unit_title'], products quantity unit type
     */
    public function getProductQuantityUnitType(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $language_id = $this->language->getId();

      $QproductsUnitType = $this->db->prepare('select pq.products_quantity_unit_title
                                              from :table_products p,
                                                   :table_products_quantity_unit pq
                                              where pq.products_quantity_unit_id = p.products_quantity_unit_id
                                              and p.products_id = :products_id
                                              and language_id = :language_id
                                              and p.products_status = 1
                                             ');
      $QproductsUnitType->bindInt(':products_id', $id);
      $QproductsUnitType->bindInt(':language_id', (int)$language_id);

      $QproductsUnitType->execute();

      return $QproductsUnitType->value('products_quantity_unit_title');
    }

    /**
     * products_shipping_delay in product info
     * @return string $products_shipping_delay, delay of the shipping
     * @access private
     */
    public function getProductsShippingDelay() :string
    {
      $language_id = $this->language->getId();

      $Qproducts = $this->db->prepare('select pd.products_shipping_delay
                                        from :table_products p,
                                             :table_products_description pd
                                        where p.products_status = 1
                                        and p.products_id = :products_id
                                        and pd.products_id = p.products_id
                                        and pd.language_id = :language_id
                                       ');

      $Qproducts->bindInt(':products_id', $this->getID());
      $Qproducts->bindInt(':language_id', (int)$language_id);
      $Qproducts->execute();

      $products_shipping_delay = $Qproducts->value('products_shipping_delay');

      if (empty($products['products_shipping_delay'])) {
        $products_shipping_delay = HTML::outputProtected(DISPLAY_SHIPPING_DELAY);
      }

      return $products_shipping_delay;
    }

    /**
     * products_shipping_delay_out_of_stock in product info
     * @return string $products_shipping_delay_out_of_stock, delay of the shipping
     * @access private
     */
    public function getProductsShippingDelayOutOfStock() :string
    {
      $language_id = $this->language->getId();

      $Qproducts = $this->db->prepare('select pd.products_shipping_delay_out_of_stock
                                        from :table_products p,
                                             :table_products_description pd
                                        where p.products_status = 1
                                        and p.products_id = :products_id
                                        and pd.products_id = p.products_id
                                        and pd.language_id = :language_id
                                       ');

      $Qproducts->bindInt(':products_id', $this->getID());
      $Qproducts->bindInt(':language_id', (int)$language_id);
      $Qproducts->execute();

      $products_shipping_delay_out_of_stock = $Qproducts->value('products_shipping_delay_out_of_stock');

      if (empty($products['products_shipping_delay_out_of_stock'])) {
        $products_shipping_delay_out_of_stock = HTML::outputProtected(DISPLAY_SHIPPING_DELAY_OUT_OF_STOCK);
      }

      return $products_shipping_delay_out_of_stock;
    }

    /**
     * @return string
     *
     */
    public function getProductsHeadTag() :string
    {
      $language_id = $this->language->getId();

      $Qproducts = $this->db->prepare('select pd.products_head_tag
                                      from :table_products p,
                                           :table_products_description pd
                                      where p.products_status = 1
                                      and p.products_id = :products_id
                                      and pd.products_id = p.products_id
                                      and pd.language_id = :language_id
                                     ');

      $Qproducts->bindInt(':products_id', $this->getID());
      $Qproducts->bindInt(':language_id', (int)$language_id);

      $Qproducts->execute();

      $products_head_tag = HTML::outputProtected($Qproducts->value('products_head_tag'));

      return $products_head_tag;
    }

    /**
     * Display products_url in product info
     * @return string $products_url, url of the product (manufacturer)
     * @access private
     */
    public function getProductsURLManufacturer() :string
    {
      $language_id = $this->language->getId();

      $Qproducts = $this->db->prepare('select pd.products_url
                                      from :table_products p,
                                           :table_products_description pd
                                      where p.products_status = 1
                                      and p.products_id = :products_id
                                      and pd.products_id = p.products_id
                                      and pd.language_id = :language_id
                                     ');

      $Qproducts->bindInt(':products_id', $this->getID());
      $Qproducts->bindInt(':language_id', (int)$language_id);

      $Qproducts->execute();

      return $Qproducts->value('products_url');
    }

    /**
     * Display products in shop and web
     * @param int|null $id
     * @return string $products_web, sell web and in the shop
     */
    public function getProductsWebAndShop(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_only_shop',  'products_only_online'], $array);

      if ($Qproducts->value('products_only_shop') != 1 && $Qproducts->value('products_only_online') != 1) {
        $products_web = '';
      }

      return $products_web;
    }

    /**
     * Display products_volume
     * @param string
     * @return string $products_volume, volume of the product
     */
    public function getProductsVolume() :string
    {
      $array = [
        'products_status' => 1,
        'products_id' => (int)$this->getID()
      ];

      $Qproducts = $this->db->get('products', ['products_volume'], $array);

      $products_volume = HTML::outputProtected($Qproducts->value('products_volume'));

      return $products_volume;
    }

    /**
     * Display products_weight
     * @param int|null $id
     * @return string $products_weight, weight of the product
     */
    private function setProductsWeight(?int $id = null)
    {
      $CLICSHOPPING_Weight = Registry::get('Weight');

      if (\is_null($id)) {
        $id = $this->getID();
      }

      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_weight', 'products_weight_class_id'], $array);

      $products_weight = $Qproducts->value('products_weight');
      $products_weight_class_id = $Qproducts->value('products_weight_class_id');

      $products_weight = $CLICSHOPPING_Weight->convert($products_weight, $products_weight_class_id, SHIPPING_WEIGHT_UNIT);

      if ($products_weight == '0.00') {
        $products_weight = '';
      }

      return $products_weight;
    }

    /**
     * Display products_weight
     * @param int|null $id
     * @return string $products_weight, weight of the product
     */
    public function getProductsWeight(?int $id = null)
    {
      return $this->setProductsWeight($id);
    }

    /**
     * Display the normal price by kilo
     * @param int|null $id
     * @return string
     */
    private function setProductsPriceByWeight(string $id = null)
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Tax = Registry::get('Tax');

      $array = [
        'products_status' => 1,
        'products_id' => (int)$id
      ];

      $Qproducts = $this->db->get('products', ['products_price', 'products_price_kilo'], $array);

      if ($this->customer->getCustomersGroupID() != 0) {
        $products_price = $this->getCustomersGroupPrice();
      } else {
        $products_price = $Qproducts->value('products_price');
      }

      $products_price_kilo = $Qproducts->value('products_price_kilo');
      $products_weight = $this->setProductsWeight();

      if ($products_price_kilo == 1 && $products_weight != '') {
        $product_price_kilo_display = $CLICSHOPPING_Currencies->displayPrice(round($products_price / $products_weight, 2), $CLICSHOPPING_Tax->getTaxRate($this->getProductsTaxClassId()));
      } else {
        $product_price_kilo_display = '';
      }

      if ((PRICES_LOGGED_IN == 'true') && !$this->customer->isLoggedOn()) {
        $product_price_kilo_display = '';
      }


      if (NOT_DISPLAY_PRICE_ZERO == 'false' && $products_price == 0) {
        $product_price_kilo_display = '';
      }

      if ($this->getPriceGroupView() == 0 && $this->customer->getCustomersGroupID() != 0) {
        $product_price_kilo_display = '';
      }

      return $product_price_kilo_display;
    }

    /**
     * Display the normal price by kilo
     * @param int|null $id
     * @return string
     */
    public function getProductsPriceByWeight(string $id = null)
    {
      return $this->setProductsPriceByWeight($id);
    }

    /**
     * the products quantity unit title
     * @param null $id
     * @return string $products_quantity_unit_['products quantity unit_title'],  name of the he products quantity unit
     */
    public function getProductsQuantityByUnit($id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $language_id = $this->language->getId();

      $QproductsQuantityUnit = $this->db->prepare('select pq.products_quantity_unit_title
                                                    from :table_products_quantity_unit pq,
                                                         :table_products p
                                                    where pq.products_quantity_unit_id = p.products_quantity_unit_id
                                                    and p.products_id = :products_id
                                                    and language_id = :language_id
                                                    ');
      $QproductsQuantityUnit->bindInt(':language_id', (int)$language_id);
      $QproductsQuantityUnit->bindInt(':products_id', $id);
      $QproductsQuantityUnit->execute();

      return $QproductsQuantityUnit->value('products_quantity_unit_title');
    }


// ---------------------------------------------------------------------------------------------------------------------------------------
// B2B
// ---------------------------------------------------------------------------------------------------------------------------------------

    /**
     * products model
     * @param int|null $id
     * @return string $products_model, model of the product
     */

    private function setProductsModel(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      if ($this->customer->getCustomersGroupID() != 0) {
        $Qproducts = $this->db->prepare('select g.products_model_group
                                          from :table_products p left join :table_products_groups g on p.products_id = g.products_id
                                          where p.products_status = 1
                                          and g.customers_group_id = :customers_group_id
                                          and g.products_group_view = 1
                                          and p.products_id = :products_id
                                         ');

        $Qproducts->bindInt(':products_id', $id);
        $Qproducts->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());

        $Qproducts->execute();
      } else {
        $Qproducts = $this->db->prepare('select products_model
                                        from :table_products
                                        where products_status = 1
                                        and products_view = 1
                                        and products_id = :products_id
                                       ');
        $Qproducts->bindInt(':products_id', $id);

        $Qproducts->execute();
      }

// display the good producs_model
      if ($this->customer->getCustomersGroupID() != 0 && !\is_null($Qproducts->value('products_model_group'))) {
        $products_model = HTML::outputProtected($Qproducts->value('products_model_group'));

        if (\is_null($Qproducts->value('products_model_group'))) {
          $products_model = HTML::outputProtected($Qproducts->value('products_model'));
        }
      } else {
        $products_model = HTML::outputProtected($Qproducts->value('products_model'));
      }

      return $products_model;
    }

    /**
     * Display products model
     * @param int
     * @return string $products_model, model of the product
     */
    public function getProductsModel(?int $id = null) :string
    {
      return $this->setProductsModel($id);
    }

    /**
     * Auto activate flash discount in product info if avalaible
     * @param int|null $id
     * @return string $flash_discount, the product flash discount based on end special end date
     * @access private
     */
    private function setProductsFlashDiscount(?int $id = null) :string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $date_format = 'Y-m-d H:i:s';

      if ($this->customer->getCustomersGroupID() != 0) {
        $QflashDiscount = $this->db->prepare('select specials_id,
                                                      products_id,
                                                      scheduled_date,
                                                      expires_date
                                                from :table_specials
                                                where products_id = :products_id
                                                and customers_group_id > 0
                                                and status = 1
                                                and flash_discount = 1
                                                and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                              ');
        $QflashDiscount->bindInt(':products_id', $id);
        $QflashDiscount->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());

      } else {
        $QflashDiscount = $this->db->prepare('select specials_id,
                                                      products_id,
                                                      scheduled_date,
                                                      expires_date,
                                                      flash_discount
                                                from :table_specials
                                                where products_id = :products_id
                                                and (customers_group_id = 0 or customers_group_id = 99)
                                                and status = 1
                                                and flash_discount = 1
                                               ');
        $QflashDiscount->bindInt(':products_id', $id);
      }

      $QflashDiscount->execute();

      $products_flash_discount = '';

      if (DateTime::getNow($date_format) >= $QflashDiscount->value('scheduled_date') && DateTime::getNow($date_format) <= $QflashDiscount->value('expires_date')) {
        $month = DateTime::getIntervalDate(DateTime::getNow($date_format), $QflashDiscount->value('expires_date'), '%M');
        $days = DateTime::getIntervalDate(DateTime::getNow($date_format), $QflashDiscount->value('expires_date'), '%D');
        $hours = DateTime::getIntervalDate(DateTime::getNow($date_format), $QflashDiscount->value('expires_date'), '%H');
        $minutes = DateTime::getIntervalDate(DateTime::getNow($date_format), $QflashDiscount->value('expires_date'), '%i');
        $secondes = DateTime::getIntervalDate(DateTime::getNow($date_format), $QflashDiscount->value('expires_date'), '%S');

        if ($month > 1) {
          $products_flash_discount = $month . ' ' . CLICSHOPPING::getDef('month') . ' ' . $days . ' ' . CLICSHOPPING::getDef('days') . ' ' . $hours . ' ' . CLICSHOPPING::getDef('hours') . ' ' . $minutes . CLICSHOPPING::getDef('minutes') . ' ' . $secondes . ' ' . CLICSHOPPING::getDef('secondes');
        } elseif ($days > 1) {
          $products_flash_discount = $days . ' ' . CLICSHOPPING::getDef('days') . ' ' . $hours . ' ' . CLICSHOPPING::getDef('hours') . ' ' . $minutes . ' ' . CLICSHOPPING::getDef('minutes') . ' ' . $secondes . ' ' . CLICSHOPPING::getDef('secondes');
        } elseif ($hours > 1) {
          $products_flash_discount = $hours . ' ' . CLICSHOPPING::getDef('hours') . ' ' . $minutes . ' ' . CLICSHOPPING::getDef('minutes') . ' ' . $secondes . ' ' . CLICSHOPPING::getDef('secondes');
        } elseif ($minutes > 1) {
          $products_flash_discount = $minutes . ' ' . CLICSHOPPING::getDef('minutes') . ' ' . $secondes . ' ' . CLICSHOPPING::getDef('secondes');
        } else {
          $products_flash_discount = $secondes . ' ' . CLICSHOPPING::getDef('secondes');
        }

        $products_flash_discount = HTML::outputProtected($products_flash_discount);
      }

      return $products_flash_discount;
    }

    /**
     * Display flash discount
     * @param int|null $id
     * @return string $products_flash_discount, flash discount
     */
    public function getProductsFlashDiscount (?int $id = null)
    {
      return $this->setProductsFlashDiscount($id);
    }


//----------------------------------------------------------------------------------------------------------------------------
// Quantity
//----------------------------------------------------------------------------------------------------------------------------


  /**
   * Display the product quantity unit title of the customer group
   * @param int|null $id
   * @return string $products_group_quantity_unit_title, the title of the product unit group (unite,douzaine....)
   * @access private
   */

    private function setProductQuantityUnitTypeCustomersGroup(?int $id = null)
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      if ($this->customer->getCustomersGroupID() != 0) {
        $QcustomerGroupPrice = $this->db->prepare('select products_quantity_unit_id_group,
                                                           products_quantity_fixed_group
                                                    from :table_products_groups
                                                    where products_id = :products_id
                                                    and customers_group_id = :customers_group_id
                                                   ');

        $QcustomerGroupPrice->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());
        $QcustomerGroupPrice->bindInt(':products_id', $id);
        $QcustomerGroupPrice->execute();

        $products_quantity_unit_id_group = $QcustomerGroupPrice->valueInt('products_quantity_unit_id_group');
        $products_quantity_fixed_group = $QcustomerGroupPrice->valueInt('products_quantity_fixed_group');

      } else {
        $products_quantity_unit_id_group = '';
        $products_quantity_fixed_group = '';
      }

      if ($this->customer->getCustomersGroupID() != 0) {

        $language_id = $this->language->getId();

        $QproductsQuantityUnit = $this->db->prepare('select products_quantity_unit_id,
                                                            language_id,
                                                            products_quantity_unit_title
                                                      from :table_products_quantity_unit
                                                      where products_quantity_unit_id = :products_quantity_unit_id
                                                      and language_id = :language_id
                                                    ');

        $QproductsQuantityUnit->bindInt(':products_quantity_unit_id', (int)$products_quantity_unit_id_group);
        $QproductsQuantityUnit->bindInt(':language_id', (int)$language_id);
        $QproductsQuantityUnit->execute();

        $products_quantity_unit = $QproductsQuantityUnit->fetch();

        $products_group_quantity_unit_title = '';

        if ($products_quantity_unit_id_group == 0) {
          $products_group_quantity_unit_title = '';
        } else {
          $products_group_quantity_unit_title = HTML::outputProtected($products_quantity_fixed_group) . ' ' . $products_quantity_unit['products_quantity_unit_title'];
        }
      } else {
        $products_group_quantity_unit_title = '';
      }

      return $products_group_quantity_unit_title;
    }

    /**
     * Display the product quantity unit title of the customer group
     * @param null $id
     * @return string $products_group_quantity_unit_title,, the title of the product unit group
     * @access private
     */
    public function getProductQuantityUnitTypeCustomersGroup($id = null)
    {
      return $this->setProductQuantityUnitTypeCustomersGroup($id);
    }


    /**
     * Return a product's minimum order quantity if available and min order quantity > 1
     * in different formular ormis shopping cart -function included in application_top
     * @param string $min_quantity_order ,
     * @access private
     */
    private function setProductsMinimumQuantity($id = null)
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $customers_group_id = $this->customer->getCustomersGroupID();

      if ($customers_group_id == 0) {
        $QproductMinOrder = $this->db->get('products', ['products_min_qty_order'], ['products_id' => (int)$id]);

        if ($QproductMinOrder->valueInt('products_min_qty_order') > 0.1) {
          $min_quantity_order = $QproductMinOrder->valueInt('products_min_qty_order');
        } else {
          $min_quantity_order = (int)MAX_MIN_IN_CART;
          if ((int)MAX_MIN_IN_CART > (int)MAX_QTY_IN_CART) {
            $min_quantity_order = (int)MAX_QTY_IN_CART;
          }
        }
      } else {
        $QcustomersGroupMinOrder = $this->db->get('customers_groups', ['customers_group_quantity_default'], ['customers_group_id' => (int)$customers_group_id]);

        $QcustomersProductsGroupMinOrder = $this->db->get('products_groups', ['products_quantity_fixed_group'], ['customers_group_id' => (int)$customers_group_id]);

        if ($QcustomersProductsGroupMinOrder->valueInt('products_quantity_fixed_group') > 1) {
          $min_quantity_order = $QcustomersProductsGroupMinOrder->valueInt('products_quantity_fixed_group');
        } elseif ($QcustomersGroupMinOrder->valueInt('customers_group_quantity_default') > 1) {
          $min_quantity_order = $QcustomersGroupMinOrder->valueInt('customers_group_quantity_default');
        } else {
          $min_quantity_order = 1;
        }
      }

      return $min_quantity_order;
    }

    /**
     * Display a product's minimum order quantity if available and min order quantity > 1
     * in different formular ormis shopping cart -function included in application_top
     * @param string $min_quantity_order ,
     * @access private
     */
    public function getProductsMinimumQuantity($id = null)
    {
      return $this->setProductsMinimumQuantity($id);
    }

    /*
      * Minimum quantity take an order for the client
      * @param string $min_order_quantity_products_display, the min of order product qty
      * @access private
    */
    private function setProductsMinimumQuantityToTakeAnOrder($id = null)
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      if ($this->getProductsMinimumQuantity($id) >= 1) {
        if ($this->getOrdersGroupView() != 0 && $this->customer->getCustomersGroupID() != 0) {
          $min_order_quantity_products_display = $this->getProductsMinimumQuantity($id);
        } elseif ($this->getProductsOrdersView() != 0 && $this->customer->getCustomersGroupID() == 0) {
          $min_order_quantity_products_display = $this->getProductsMinimumQuantity($id);
        } else {
          $min_order_quantity_products_display = '';
        }
      } else {
        $min_order_quantity_products_display = '';
      }

      if (PRICES_LOGGED_IN == 'true' && !$this->customer->isLoggedOn()) {
        $min_order_quantity_products_display = '';
      }

      return $min_order_quantity_products_display;
    }


    /*
      * display Minimum quantity take an order for the client
      * @param string $min_order_quantity_products_display, the min of order product qty
    */
    public function getProductsMinimumQuantityToTakeAnOrder($id = null)
    {
      return $this->setProductsMinimumQuantityToTakeAnOrder($id);
    }

    /**
     * Display the quantity for the customer
     * @param string $input_quantity , the quantity for the customer
     * @access private
     */

    public function setProductsAllowingToInsertQuantity($id = null)
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      if ($this->customer->getCustomersGroupID() != 0 && $this->getOrdersGroupView() != 0) {
        $input_quantity = '<label for="Quantity' . $id . '" class="visually-hidden"></label>';
        $input_quantity .= HTML::inputField('cart_quantity', (int)$this->setProductsMinimumQuantityToTakeAnOrder($id), 'id="Quantity' . $id . '" placeholder="qty" class="input-small" maxlength="4" size="4" min="1"') . '&nbsp;&nbsp;';
      } else {
        $input_quantity = '';
      }

      if ($this->customer->getCustomersGroupID() == 0 && $this->getProductsOrdersView() != 0) {
        if (PRICES_LOGGED_IN == 'false') {
          $input_quantity = '<label for="Quantity' . $id . '" class="visually-hidden"></label>';
          $input_quantity .= HTML::inputField('cart_quantity', (int)$this->setProductsMinimumQuantityToTakeAnOrder($id), 'id="Quantity' . $id . '" placeholder="qty" class="input-small" maxlength="4" size="4" min="1"') . '&nbsp;&nbsp;';
        } elseif (PRICES_LOGGED_IN == 'true' && $this->customer->isLoggedOn()) {
          $input_quantity = '<label for="Quantity' . $id . '" class="visually-hidden"></label>';
          $input_quantity .= HTML::inputField('cart_quantity', (int)$this->setProductsMinimumQuantityToTakeAnOrder($id), 'id="Quantity' . $id . '" placeholder="qty" class="input-small" maxlength="4" size="4" min="1"') . '&nbsp;&nbsp;';
        } else {
          $input_quantity = '';
        }
      }

      if ($this->setProductsMinimumQuantityToTakeAnOrder($id) == 0 && MAX_MIN_IN_CART == 0) {
        $input_quantity = '';
      }

      if ($this->getPriceGroupView() == 0 && $this->customer->getCustomersGroupID() != 0) {
        $input_quantity = '';
      }

      return $input_quantity;
    }

    /**
     * Display the quantity for the customer
     * @param null $id
     * @return string
     */
    public function getProductsAllowingToInsertQuantity($id = null)
    {
      return $this->setProductsAllowingToInsertQuantity($id);
    }

// ------------------------------------------------------------------------------------------------------
// Message && button
// ------------------------------------------------------------------------------------------------------

    /**
     * Display a message in function the customer group applied
     * @return string
     */
    public function getProductsAllowingTakeAnOrderMessage() :string
    {
      $submit_button_view = '';

      if (($this->customer->getCustomersGroupID() != 0) && ($this->getOrdersGroupView() != 1)) {
        $submit_button_view = '<div class="submitButtonView">' . CLICSHOPPING::getDef('no_orders_group') . '</div>';
      } elseif (($this->customer->getCustomersGroupID() == 0) && ($this->getProductsOrdersView() != 1)) {
        $submit_button_view = '<div class="submitButtonView">' . CLICSHOPPING::getDef('no_orders_public') . '</div>';
      }

      return $submit_button_view;
    }

    /**
     * @param $button
     * @return mixed
     */
    public function getBuyButton($button)
    {
      $this->button = $button;

      return $button;
    }

    /**
     * Button buy now
     * @return string
     * @access private
     */

    public function setProductsBuyButton() :string
    {
      $buy_button = $this->button;

      if ((PRICES_LOGGED_IN == 'true' && !$this->customer->isLoggedOn())) {
        $buy_button = '';
      } elseif ($this->getProductsOrdersView() == 0 && $this->customer->getCustomersGroupID() == 0) {
        $buy_button = '';
      }

      if (PRICES_LOGGED_IN == 'true' && $this->customer->isLoggedOn()) {
        if ($this->getProductsOrdersView() == 0 && $this->customer->getCustomersGroupID() == 0) {
          $buy_button = '';
        } elseif ($this->getOrdersGroupView() == 0 && $this->customer->getCustomersGroupID() != 0) {
          $buy_button = '';
        }
      } elseif (PRICES_LOGGED_IN == 'false' && $this->customer->isLoggedOn()) {
        if ($this->getProductsOrdersView() == 0 && $this->customer->getCustomersGroupID() == 0) {
          $buy_button = '';
        } elseif ($this->getOrdersGroupView() == 0 && $this->customer->getCustomersGroupID() != 0) {
          $buy_button = '';
        }
      }

      if ($this->getPriceGroupView() == 0 && $this->customer->getCustomersGroupID() != 0) {
        $buy_button = '';
      }

      return $buy_button;
    }

    /**
     * Button buy now
     * @return string
     */
    public function getProductsBuyButton() :string
    {
      return $this->setProductsBuyButton();
    }

    /**
     * Return a products button sold out
     * @param null $button_type
     * @return string $product_button_sold_out,the bootstrap item
     */
    private function getProductButtonSoldOut($button_type = null) :string
    {
      $product_button_sold_out = '';

      if (\is_null($button_type)) {
        $button_type = 'btn-warning btn-sm';
      }

      if (PRE_ORDER_AUTORISATION == 'false') {
        $product_button_sold_out = '<button type="button" class="btn ' . $button_type . '">' . CLICSHOPPING::getDef('button_sold_out') . '</button>';
      }

      return $product_button_sold_out;
    }

    /**
     * Products sold out
     * Check if the required stock is available for display a button Product sold out
     * @param $id
     * @param null $button_type , bootstrap button bootstrap item
     * @return string
     */

    private function setProductsSoldOut($id, $button_type = null): string
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }
      $product_sold_out = '';

      $QproductSoldOut = $this->db->prepare('select products_quantity
                                              from :table_products
                                              where products_id = :products_id
                                              and products_quantity < 1
                                             ');

      $QproductSoldOut->bindInt(':products_id', $id);
      $QproductSoldOut->execute();

      if ($QproductSoldOut->fetch()) {
        if (STOCK_CHECK == 'true' && STOCK_ALLOW_CHECKOUT == 'false' && PRICES_LOGGED_IN == 'false') {
          $product_sold_out = $this->getProductButtonSoldOut($button_type);
        } elseif (PRICES_LOGGED_IN == 'true' && $this->customer->getCustomersGroupID() == 0 && !$this->customer->isLoggedOn() && STOCK_CHECK == 'true' && STOCK_ALLOW_CHECKOUT == 'false') {
          $product_sold_out = ' ';
        } elseif (PRICES_LOGGED_IN == 'true' && $this->customer->getCustomersGroupID() != 0 && STOCK_CHECK == 'true' && STOCK_ALLOW_CHECKOUT == 'false') {
          $product_sold_out = $this->getProductButtonSoldOut($button_type);
        } elseif (PRICES_LOGGED_IN == 'true' && $this->customer->getCustomersGroupID() == 0 && $this->customer->isLoggedOn() && STOCK_CHECK == 'true' && STOCK_ALLOW_CHECKOUT == 'false') {
          $product_sold_out = $this->getProductButtonSoldOut($button_type);
        }
      }

      return $product_sold_out;
    }

    /**
     * Display Products sold out
     * @param null $id
     * @param null $button_type : bootstrap button bootstrap item
     * @return string
     */
    public function getProductsSoldOut($id = null, $button_type = null) :string
    {
      return $this->setProductsSoldOut($id, $button_type);
    }


// =======================================================================================================================================================
// Price & tax
//=======================================================================================================================================================

    /**
     * Display the price in different mode B2B or not
     * @param null $id
     * @return float|string
     * @access private
     */

    private function setCustomersPrice($id = null)
    {

      if (PRICES_LOGGED_IN == 'false') {
        $product_price = $this->setCalculPrice($id);
      }

      if ((PRICES_LOGGED_IN == 'true') && (!$this->customer->isLoggedOn())) {
        $product_price = HTML::link(CLICSHOPPING::link(null, 'Account&LogIn'), CLICSHOPPING::getDef('prices_logged_in_text')) . '&nbsp;';
      } else {
        $product_price = $this->setCalculPrice($id);
      }

      if ($this->getPriceGroupView() == 0 && $this->customer->getCustomersGroupID() != 0) {
        $product_price = '';
      }

      return $product_price;
    }

    /**
     * Display the price in different mode B2B or not
     * @param null $id
     * @return float|string
     */
    public function getCustomersPrice($id = null)
    {
      return $this->setCustomersPrice($id);
    }

    /**
     * Return a product's special price B2B (returns nothing if there is no offer
     * @param null $id
     * @return float $product['specials_new_products_price'] the special price
     * TABLES: products B2B
     */

    private function setSpecialPriceGroup($id = null)
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      if ($this->customer->getCustomersGroupID() != 0) {
        $Qproducts = $this->db->prepare('select distinct specials_new_products_price
                                         from :table_specials
                                         where products_id = :products_id
                                         and status = 1
                                         and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                        ');
        $Qproducts->bindInt(':products_id', $id);
        $Qproducts->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());

      } else {
        $Qproducts = $this->db->prepare('select distinct specials_new_products_price
                                         from :table_specials
                                         where products_id = :products_id
                                         and status = 1
                                         and (customers_group_id = 0 or customers_group_id = 99)
                                        ');

        $Qproducts->bindInt(':products_id', $id);
      }

      $Qproducts->execute();

      return $Qproducts->valueDecimal('specials_new_products_price');
    }

    /**
     * Product price and price groupwithouth taxe and symbol
     * @param null $id
     * @return float $products_price, the product group price
     */

    private function setPrice($id = null)
    {

      if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qproduct = $this->db->prepare('select products_id,
                                             products_price
                                       from :table_products
                                       where products_status = 1
                                       and products_id = :products_id
                                       and products_archive = 0
                                     ');
      $Qproduct->bindInt(':products_id', $id);

      $Qproduct->execute();

      if ($this->customer->getCustomersGroupID() != 0) {
        $QcustomerGroupPrice = $this->db->prepare('select customers_group_price,
                                                          price_group_view
                                                  from :table_products_groups
                                                  where products_id = :products_id
                                                  and customers_group_id =  :customers_group_id
                                                  ');
        $QcustomerGroupPrice->bindInt(':customers_group_id', (int)$this->customer->getCustomersGroupID());
        $QcustomerGroupPrice->bindInt(':products_id', $Qproduct->valueInt('products_id'));
        $QcustomerGroupPrice->execute();

        if ($QcustomerGroupPrice->fetch()) {
          if ($QcustomerGroupPrice->valueInt('price_group_view') == 1) {
            $products_price = $QcustomerGroupPrice->valueDecimal('customers_group_price');
          } else {
            $products_price = $Qproduct->valueDecimal('products_price');
          }
        } else {
          $products_price = $Qproduct->valueDecimal('products_price');
        }
      } else {
        $products_price = $Qproduct->valueDecimal('products_price');
      }

      return $products_price;
    }

    /**
     * Product group price with taxe and symbol (if authorize)
     * @param null $id
     * @return float $products_price, the product price
     */
    public function setDisplayPriceGroup($id = null)
    {
      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Tax = Registry::get('Tax');

      if (\is_null($id)) {
        $id = $this->getID();
      }

      $products_price = $CLICSHOPPING_Currencies->displayPrice($this->setPrice($id), $CLICSHOPPING_Tax->getTaxRate($this->getProductsTaxClassId()));

      return $products_price;
    }

    /**
     * Product group price withiout taxe
     * @param null $id
     * @return float $products_price, the product price
     */

    public function getDisplayPriceGroupWithoutCurrencies($id = null): float
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $products_price = $this->setPrice($id);

      return $products_price;
    }

    /**
     * Calcul the different price in function the group
     * @param null $id
     * @return float
     * @access private
     */
    private function setCalculPrice($id = null)
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $CLICSHOPPING_Currencies = Registry::get('Currencies');
      $CLICSHOPPING_Tax = Registry::get('Tax');

      $normal_price = 0;

      if ($this->customer->getCustomersGroupID() != 0) {
        $Qproducts = $this->db->prepare('select g.price_group_view
                                          from :table_products p left join :table_products_groups g on p.products_id = g.products_id
                                          where p.products_status = 1
                                          and g.customers_group_id = :customers_group_id
                                          and g.products_group_view = 1
                                          and p.products_id = :products_id
                                          order by p.products_date_added DESC
                                         ');

        $Qproducts->bindInt(':products_id', $id);
        $Qproducts->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

        $Qproducts->execute();

        $price_group_view = $Qproducts->valueInt('price_group_view');

//do not change
        if ($new_price = $this->setSpecialPriceGroup($id)) {
          if ($price_group_view == 1) {
            $products_price = '<span class="normalPrice"><del>' . $this->setDisplayPriceGroup($id) . '</del></span><span class="specialPrice">' . $CLICSHOPPING_Currencies->displayPrice($new_price, $CLICSHOPPING_Tax->getTaxRate($this->getProductsTaxClassId())) . '</span>';
          } else {
            $products_price = $this->setDisplayPriceGroup($id);
          }
        } else {
          $products_price = $this->setDisplayPriceGroup($id);
        }
      } else {
        $normal_price = 1; // Arret du mode Grand public pour refus d'afficher le prix groupe B2B
      }

      if (($this->customer->getCustomersGroupID() == 0) || ($normal_price == 1)) {
//do not change
        if ($new_price = $this->setSpecialPriceGroup($id)) {
          $products_price = '<span class="normalPrice"><del>' . $this->setDisplayPriceGroup($id) . '</del></span><span class="specialPrice">' . $CLICSHOPPING_Currencies->displayPrice($new_price, $CLICSHOPPING_Tax->getTaxRate($this->getProductsTaxClassId())) . '</span>';
        } else {

          $products_price = $this->setDisplayPriceGroup($id);
        }
      }

      return $products_price;
    }

    /**
     * Display the price in function the group
     * @param null $id
     * @return float|string
     * @access private
     */
    public function getCalculPrice($id = null)
    {
      return $this->setCalculPrice($id);
    }

    /**
     * Return a product's stock
     * @param null $id , id product
     * @return int $stock_values['products_quantity']
     */

    public function getProductsStock($id = null): int
    {
      $CLICSHOPPING_Prod = Registry::get('Prod');

      if (\is_null($id)) {
        $id = $CLICSHOPPING_Prod::getProductID($this->getID());
      } else {
        $id = $CLICSHOPPING_Prod::getProductID($id);
      }

      $Qproduct = $this->db->get('products', ['products_quantity'], ['products_id' => (int)$id]);

      return $Qproduct->valueInt('products_quantity');
    }

    /**
     * Return a out of stock on the products
     * Check if the required stock is available
     * If insufficent stock is available return an out of stock message
     * @param string $id , id product
     * @param string $products_quantity
     * @return string $out_of_stock
     */

    public function getCheckStock($id, $products_quantity) :string
    {
      $stock_left = $this->getProductsStock($id) - $products_quantity;
      $out_of_stock = '';

      if ($stock_left < 0) {
        if (PRE_ORDER_AUTORISATION == 'true') {
          $out_of_stock = '<span class="markProductOutOfStock" id="markProductOutOfStock">' . CLICSHOPPING::getDef('text_out_of_stock_pre_order') . '</span>';
        } elseif (STOCK_ALLOW_CHECKOUT == 'true') {
          $out_of_stock = '<span class="markProductOutOfStock" id="markProductOutOfStock">' . CLICSHOPPING::getDef('text_out_of_stock_allow_checkout') . '</span>';
        } else {
          $out_of_stock = '<span class="markProductOutOfStock" id="markProductOutOfStock">' . CLICSHOPPING::getDef('text_out_of_stock') . '</span>';
        }
      }

      return $out_of_stock;
    }

    /**
     * Return an image concerning the stock
     * @param string $id , id product
     * @return string the $display_stock_values, the image value of stock
     */
    public function getDisplayProductsStock($id) :string
    {
      $display_products_stock = $this->getProductsStock($id);

      if ($display_products_stock > STOCK_REORDER_LEVEL) {
        $display_stock_values = HTML::tickerImage(CLICSHOPPING::getDef('text_in_stock'), 'ModulesTickerBootstrapTickerStockGood', true);
      } elseif ($display_products_stock <= STOCK_REORDER_LEVEL && $display_products_stock > 0) {
        $display_stock_values = HTML::tickerImage(CLICSHOPPING::getDef('text_alert_stock'), 'ModulesTickerBootstrapTickerStockWarning', true);
      } else {
        $display_stock_values = HTML::tickerImage(CLICSHOPPING::getDef('text_out_of_stock'), 'ModulesTickerBootstrapTickerStockDanger', true);
      }

      return $display_stock_values;
    }

    /**
     * Count the number of attributes on product
     * @param string
     * @return string $products_attributes['total'], total of attributes
     */
    private function setCountProductsAttributes($id = null)
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $language_id = $this->language->getId();

      $QproductsAttributes = $this->db->prepare('select count(*) as total
                                                from :table_products_options popt,
                                                     :table_products_attributes patrib
                                                where patrib.products_id = :products_id
                                                and patrib.options_id = popt.products_options_id
                                                and popt.language_id = :language_id
                                                and patrib.status = 1
                                               ');
      $QproductsAttributes->bindInt(':products_id', $id);
      $QproductsAttributes->bindInt(':language_id', (int)$language_id);

      $QproductsAttributes->execute();

      $products_attributes = $QproductsAttributes->fetch();

      return $products_attributes['total'];
    }

    /**
     * @param null $id
     * @return string
     */
    Public function getCountProductsAttributes($id = null)
    {
      return $this->SetCountProductsAttributes($id);
    }

    /**
     * Check if product has attributes
     * @param string $id , id product
     * @return the checking of the products attributbes
     */
    public function getHasProductAttributes($id = null)
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qattributes = $this->db->prepare('select products_id
                                        from :table_products_attributes
                                        where products_id = :products_id
                                        and status = 1
                                        limit 1
                                        ');
      $Qattributes->bindInt(':products_id', $id);

      $Qattributes->execute();

      return $Qattributes->fetch() !== false;
    }

    /**
     *  Return a product's special price (returns nothing if there is no offer)
     * @param null $id
     * @return mixed
     */
    private function setProductsSpecialPrice($id = null)
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qproduct = $this->db->prepare('select distinct customers_group_id,
                                                      specials_new_products_price
                                     from :table_specials
                                     where products_id = :products_id
                                     and status = 1
                                     and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                   ');
      $Qproduct->bindInt(':products_id', $id);
      $Qproduct->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

      $Qproduct->execute();

      if ($Qproduct->fetch() !== false) {
        $result = $Qproduct->valueDecimal('specials_new_products_price');

        return $result;
      }
    }

    /**
     *  Return a product's special price (returns nothing if there is no offer)
     * @param null $id
     * @return mixed
     */
    public function getProductsSpecialPrice($id = null)
    {
      return $this->setProductsSpecialPrice($id);
    }

//===================================================================================================================================

    /**
     * Display the name of manufacturer
     * @return string
     * @access private
     */
    private function setManufacturersName(): string
    {
      $language_id = $this->language->getId();

      $Qmanufacturers = $this->db->prepare('select manufacturer_name
                                             from :table_manufacturers_info
                                             where manufacturers_id = :manufacturers_id
                                             and languages_id = :languages_id
                                           ');
      $Qmanufacturers->bindInt(':manufacturers_id', (int)$_GET['manufacturersId']);
      $Qmanufacturers->bindInt(':languages_id', (int)$language_id);

      $Qmanufacturers->execute();

      return $Qmanufacturers->value('manufacturer_name');
    }

    /**
     * @return array
     */
    public function getManufacturersName() :array
    {
      $this->setManufacturersName();
    }

    /**
     * Display a description of manufacturer
     * @param $id
     * @return string
     * @access private
     */
    private function setManufacturersDescription($id): string
    {
      $language_id = $this->language->getId();

      $Qmanufacturers = $this->db->prepare('select manufacturer_description
                                           from :table_manufacturers_info
                                           where manufacturers_id = :manufacturers_id
                                           and languages_id = :languages_id
                                         ');
      $Qmanufacturers->bindInt(':manufacturers_id', (int)$id);
      $Qmanufacturers->bindInt(':languages_id', (int)$language_id);

      $Qmanufacturers->execute();

      return $Qmanufacturers->value('manufacturer_description');
    }

    /**
     * @return string
     */
    public function getManufacturersDescription() :string
    {
      $id = HTML::sanitize($_GET['manufacturersId']);

      return $this->setManufacturersDescription($id);
    }

    /**
     * Display a description of manufacturer
     * @param int $_GET ['manufacturers_id']) the id of manufacturer
     * @param string $manufacturers ['manufacturer_description'], The description of manufacturer
     * @access private
     * @return
     */
    private function setManufacturersImage(): string
    {
      $Qmanufacturers = $this->db->prepare('select manufacturer_image
                                             from :table_manufacturers
                                             where manufacturers_id = :manufacturers_id
                                           ');
      $Qmanufacturers->bindInt(':manufacturers_id', (int)$_GET['manufacturersId']);

      $Qmanufacturers->execute();

      return $Qmanufacturers->value('manufacturer_image');
    }

    /**
     * Display a description of manufacturer
     * @return string
     */
    public function getManufacturersImage() :string
    {
      return $this->setManufacturersImage();
    }

    /**
     * Display a  manufacturers under an array
     * @return array
     */

    public function setManufacturersDropDown() :array
    {
      $manufacturers_array = [];
      
      $manufacturers_array[] = [
        'id' => '',
        'text' => CLICSHOPPING::getDef('modules_advanced_search_manufacturers_text_all_manufacturers')
      ];

      $Qmanufacturers = $this->db->prepare('select manufacturers_id,
                                                    manufacturers_name
                                             from :table_manufacturers
                                             where manufacturers_status = :manufacturers_status
                                             order by manufacturers_name
                                           ');

      $Qmanufacturers->bindValue(':manufacturers_status', 0);
      $Qmanufacturers->execute();

      while ($Qmanufacturers->fetch()) {
        $manufacturers_array[] = [
          'id' => $Qmanufacturers->valueInt('manufacturersId'),
          'text' => $Qmanufacturers->value('manufacturers_name')
        ];
      }

      return $manufacturers_array;
    }

    /**
     * Display a manufacturer in dropdown
     * @return array
     */
    public function getManufacturersDropDown(): array
    {
      return $this->setManufacturersDropDown();
    }

    /**
     * Display a a ticker on products new (text)
     * @param null $id
     * @return bool $ticker (true or false)
     */
    private function setProductsTickerProductsNew($id = null) :bool
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qproducts = $this->db->prepare('select products_id,
                                             products_date_added
                                      from :table_products
                                      where products_status = 1
                                      and products_view = 1
                                      and products_id = :products_id
                                      and products_archive = 0
                                     ');

      $Qproducts->bindInt(':products_id', $id);

      $Qproducts->execute();

// 2592000 = 30 days in the unix timestamp format
      $day_new_products = 86400 * (int)DAY_NEW_PRODUCTS_ARRIVAL;
      $today_time = time();

      if (($today_time - strtotime($Qproducts->value('products_date_added'))) < $day_new_products) {
        $ticker = true;
      } else {
        $ticker = false;
      }

      return $ticker;
    }

    /**
     * display a ticker css for new price
     * @param null $id
     * @return bool $ticker, product new procust price ticker
     */
    public function getProductsTickerProductsNew($id = null) :bool
    {
      return $this->setProductsTickerProductsNew($id);
    }

    /**
     * Display a ticker on special price (text)
     * @param null $id
     * @return bool $ticker (true or false)
     */
    private function setProductsTickerSpecials($id = null) :bool
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      if ($this->customer->getCustomersGroupID() != 0) {
        $Qproducts = $this->db->prepare('select distinct specials_date_added
                                          from  :table_specials
                                          where status = 1
                                          and products_id = :products_id
                                          and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                         ');
        $Qproducts->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());
      } else {
        $Qproducts = $this->db->prepare('select distinct specials_date_added
                                          from  :table_specials
                                          where status = 1
                                          and products_id = :products_id
                                          and (customers_group_id = 0 or customers_group_id = 99)
                                        ');
      }

      $Qproducts->bindInt(':products_id', $id);

      $Qproducts->execute();

// 2592000 = 30 days in the unix timestamp format
      $day_new_products = 86400 * (int)DAY_NEW_PRODUCTS_ARRIVAL;
      $today_time = time();

      if (($today_time - strtotime($Qproducts->value('specials_date_added'))) < $day_new_products) {
        $ticker = true;
      } else {
        $ticker = false;
      }

      return $ticker;
    }

    /**
     * display a ticker css for specials price
     * @param string
     * @return string $ticker, specials price ticker
     */
    public function getProductsTickerSpecials($id = null)
    {
      return $this->setProductsTickerSpecials($id);
    }

    /**
     * display a ticker pourcentage css
     * @param string
     * @return bool|string $ticker, specials price ticker
     */
    public function getProductsTickerSpecialsPourcentage($id, string $tag = ' %') :bool|string
    {
      if ($this->setSpecialPriceGroup($id) != 0 && $this->setPrice($id) != 0) {
        $pourcentage_price = (round((($this->setSpecialPriceGroup($id) / $this->setPrice($id))), 2));
        $pourcentage_price = ((1 - $pourcentage_price) * (-100)) . $tag;

        return $pourcentage_price;
      } else {
        return '';
      }
    }

    /**
     * Display a a ticker on product favorites
     * @param null $id
     * @return bool $ticker (true or false)
     * @access private
     */
    private function setProductsTickerFavorites($id = null) :bool
    {
     if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qproducts = $this->db->prepare('select distinct products_favorites_date_added
                                       from :table_products_favorites
                                       where status = 1
                                      and products_id = :products_id
                                     ');

      $Qproducts->bindInt(':products_id', $id);
      $Qproducts->execute();

// 2592000 = 30 days in the unix timestamp format
      $day_new_products = 86400 * DAY_NEW_PRODUCTS_ARRIVAL;
      $today_time = time();

      if (($today_time - strtotime($Qproducts->value('products_favorites_date_added'))) < $day_new_products) {
        $ticker = true;
      } else {
        $ticker = false;
      }

      return $ticker;
    }

    /**
     * Display a a ticker on product featured
     * @param null $id
     * @return bool $ticker (true or false)
     */
    private function setProductsTickerFeatured($id = null) :bool
    {
      if (\is_null($id)) {
        $id = $this->getID();
      }

      $Qproducts = $this->db->prepare('select distinct products_featured_date_added
                                        from  :table_products_featured
                                        where status = 1
                                        and products_id = :products_id
                                       ');

      $Qproducts->bindInt(':products_id', $id);

      $Qproducts->execute();

// 2592000 = 30 days in the unix timestamp format
      $day_new_products = 86400 * DAY_NEW_PRODUCTS_ARRIVAL;
      $today_time = time();

      if (($today_time - strtotime($Qproducts->value('products_featured_date_added'))) < $day_new_products) {
        $ticker = true;
      } else {
        $ticker = false;
      }

      return $ticker;
    }

    /**
     * display a ticker css for specials price
     * @param string
     * @return bool $ticker, favorites ticker
     */
    public function getProductsTickerFavorites($id = null)
    {
      return $this->setProductsTickerFavorites($id);
    }

    /**
     * display a ticker css for featured
     * @param string
     * @return string $ticker, specials price ticker
     */
    public function getProductsTickerFeatured($id = null)
    {
      return $this->setProductsTickerFeatured($id);
    }


    /*
    * display Save Money by the customer
    * @param string
    * @return string $save_money, the difference between real price and  specials
    * @access private
    */
    private function setProductsSaveMoneyCustomer(int|string $id) :?float
    {
      $savemoney = 0;

      if ($this->setSpecialPriceGroup($id) != 0 && $this->setPrice($id) != 0) {
        $savemoney = (round((($this->setPrice($id) - $this->setSpecialPriceGroup($id))), 4));
      }

      return $savemoney;
    }

    /*
    * display a save Money by the customer
    * @param int|string
    * @return string $save_money, the difference between real price and  specials
    */
    public function getProductsSaveMoneyCustomer(int|string $id) :?float
    {
      return $this->setProductsSaveMoneyCustomer($id);
    }

    /**
     * @param null $id
     * @param null $qty
     * @param null $products_price
     * @return false|float|int|mixed
     */
    public function getProductsNewPriceByDiscountByQuantity($id = null, $qty = null, $products_price = null)
    {
      $QprodutsQuantityDiscount = $this->db->prepare('select discount_quantity,
                                                            discount_customer
                                                      from :table_products_discount_quantity
                                                      where products_id = :products_id
                                                      and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                                      and discount_quantity <> 0
                                                    ');
      $QprodutsQuantityDiscount->bindInt(':products_id', $id);
      $QprodutsQuantityDiscount->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

      $QprodutsQuantityDiscount->execute();

      $discount_quantity = [];
      $discount_customer = [];

      while ($QprodutsQuantityDiscount->fetch()) {
        $discount_quantity[] = $QprodutsQuantityDiscount->valueInt('discount_quantity');
        $discount_customer[] = $QprodutsQuantityDiscount->valueDecimal('discount_customer');
      }

      $nb_discount = $QprodutsQuantityDiscount->rowCount(); // dans ton exemple 3 discounts

      $new_discount_price = null;

      $i = $nb_discount - 1; // 0,1,2 pour les indices des tableaux de ton example

      for ($i; $i > -1; $i--) {
        if ($qty >= $discount_quantity[$i]) {
          $new_discount_price = ($products_price - ($products_price * ($discount_customer[$i] / 100)));
          $_SESSION['ProductsID'] = $id;
        }
      }

      if (!\is_null($new_discount_price) || !empty($new_discount_price)) {
        return $new_discount_price;
      } else {
        return false;
      }
    }

    /**
     *  Get the price by quantity discount for the shopping cart
     * @param null $id
     * @param null $qty
     * @param null $products_price
     * @return float|int
     */
    public function getInfoPriceDiscountByQuantityShoppingCart($id = null, $qty = null, $products_price = null)
    {
      $QprodutsQuantityDiscount = $this->db->prepare('select discount_quantity,
                                                            discount_customer
                                                      from :table_products_discount_quantity
                                                      where products_id = :products_id
                                                      and (customers_group_id = :customers_group_id or customers_group_id = 99)
                                                      and discount_quantity <> 0
                                                    ');
      $QprodutsQuantityDiscount->bindInt(':products_id', $id);
      $QprodutsQuantityDiscount->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());

      $QprodutsQuantityDiscount->execute();

      while ($QprodutsQuantityDiscount->fetch()) {
        $discount_quantity[] = $QprodutsQuantityDiscount->valueInt('discount_quantity');
        $discount_customer[] = $QprodutsQuantityDiscount->valueDecimal('discount_customer');
      }

      $nb_discount = $QprodutsQuantityDiscount->rowCount(); // dans ton exemple 3 discounts
      $discount = 0;

      $i = $nb_discount - 1; // 0,1,2 pour les indices des tableaux de ton exemple

      for ($i; $i > -1; $i--) {
        if ($qty >= $discount_quantity[$i]) {
          $new_discount_price = ($products_price - ($products_price * ($discount_customer[$i] / 100)));

          $_SESSION['ProductsID'] = $id;

          $discount = ($products_price - $new_discount_price) * $qty;
        }
      }

      return $discount;
    }

    /*
    * Return table heading with sorting capabilities
    */
    public function createSortHeading($sortby, $column, $heading)
    {
      if (isset($_POST['keywords'])) {
        $keywords = HTML::sanitize($_POST['keywords']);
      } elseif (isset($_GET['keywords'])) {
        $keywords = HTML::sanitize($_GET['keywords']);
      } else {
        $keywords = '';
      }

      if (isset($sortby)) {
        if (isset($_POST['keywords']) || isset($_GET['keywords'])) {
          $sort_prefix = '<a href="' . CLICSHOPPING::link(CLICSHOPPING::getIndex(), CLICSHOPPING::getAllGET(array('page', 'info', 'sort')) . '&keywords=' . $keywords . '&page=1&sort=' . $column . ($sortby == $column . 'a' ? 'd' : 'a')) . '" title="' . HTML::output(CLICSHOPPING::getDef('text_sort_products') . ($sortby == $column . 'd' || substr($sortby, 0, 1) != $column ? CLICSHOPPING::getDef('text_ascendingly') : CLICSHOPPING::getDef('text_descendingly')) . CLICSHOPPING::getDef('text_by') . $heading) . '" class="productListing-heading">';
          $sort_suffix = ' ' . (substr($sortby, 0, 1) == $column ? (substr($sortby, 1, 1) == 'a' ? '+' : '-') : '') . '</a>';
        } else {
          $sort_prefix = '<a href="' . CLICSHOPPING::link(null, CLICSHOPPING::getAllGET(array('page', 'info', 'sort')) . '&page=1&sort=' . $column . ($sortby == $column . 'a' ? 'd' : 'a')) . '" title="' . HTML::output(CLICSHOPPING::getDef('text_sort_products') . ($sortby == $column . 'd' || substr($sortby, 0, 1) != $column ? CLICSHOPPING::getDef('text_ascendingly') : CLICSHOPPING::getDef('text_descendingly')) . CLICSHOPPING::getDef('text_by') . $heading) . '" class="productListing-heading">';
          $sort_suffix = ' ' . (substr($sortby, 0, 1) == $column ? (substr($sortby, 1, 1) == 'a' ? '+' : '-') : '') . '</a>';
        }
      } else {
        $sort_prefix = '<a href="' . CLICSHOPPING::link(CLICSHOPPING::getIndex(), CLICSHOPPING::getAllGET(array('page', 'info', 'sort')) . '&keywords=' . $keywords . '&page=1&sort=' . $column . ($sortby == $column . 'a' ? 'd' : 'a')) . '" title="' . HTML::output(CLICSHOPPING::getDef('text_sort_products') . ($sortby == $column . 'd' || substr($sortby, 0, 1) != $column ? CLICSHOPPING::getDef('text_ascendingly') : CLICSHOPPING::getDef('text_descendingly')) . CLICSHOPPING::getDef('text_by') . $heading) . '" class="productListing-heading">';
        $sort_suffix = ' ' . (substr($sortby, 0, 1) == $column ? (substr($sortby, 1, 1) == 'a' ? '+' : '-') : '') . '</a>';
      }

      return $sort_prefix . $heading . $sort_suffix;
    }

    /*
    * Return the class_id of the product
    * @param Int $id, id product
    * @return Int products_weight_class_id, Id of the weight class
    */
    private function setWeightClassIdByProducts($id)
    {
      $QweightClass = $this->db->prepare('select products_weight_class_id
                                           from :table_products
                                           where products_id = :products_id
                                         ');
      $QweightClass->bindInt(':products_id', $id);
      $QweightClass->execute();

      return $QweightClass->value('products_weight_class_id');
    }

    /*
    * Display the class_id of the product
    * @param Int $id, id product
    * @return Int products_weight_class_id, Id of the weight class
    */
    public function getWeightClassIdByProducts($id)
    {
      $id = HTML::sanitize($id);
      return $this->setWeightClassIdByProducts($id);
    }

    /**
     * @param int $weight_class_id
     * @return string
     */
    private function setSymbolWeightByProducts(int $weight_class_id) :string
    {
      $QweightSymbol = $this->db->prepare('select weight_class_key
                                           from :table_weight_classes
                                           where weight_class_id = :products_weight_class_id
                                           and language_id = :language_id
                                         ');
      $QweightSymbol->bindInt(':products_weight_class_id', $weight_class_id);
      $QweightSymbol->bindInt(':language_id', $this->language->getId());
      $QweightSymbol->execute();

      return $QweightSymbol->value('weight_class_key');
    }

    /**
     * @param int $weight_class_id
     * @return mixed
     */
    public function getSymbolWeightByProducts(int $weight_class_id)
    {
      $weight_class_id = HTML::sanitize($weight_class_id);

      return $this->setSymbolWeightByProducts($weight_class_id);
    }

  } // end class