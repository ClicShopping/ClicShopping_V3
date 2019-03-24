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

  namespace ClicShopping\Apps\Catalog\Products\Classes\Shop;

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\DateTime;

  class ProductsFunctionTemplate {

    protected $productsCommon;
    protected $customer;
    protected $template;
    protected $category;
    protected $rewriteUrl;

    public function __construct() {
      $this->productsCommon = Registry::get('ProductsCommon');
      $this->customer = Registry::get('Customer');
      $this->template = Registry::get('Template');
      $this->category = Registry::get('Category');
      $this->rewriteUrl = Registry::get('RewriteUrl');
    }

/**
 * product url
 * @return bool|mixed
 */
    public function getProductsUrlRewrited() {
      return $this->rewriteUrl;
    }

    public function getProductsNameUrl($products_id, $in_stock) {
      $products_name = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), '<span itemprop="name">' . $this->productsCommon->getProductsName($products_id) .'</span>', 'itemprop="url"');

      return $products_name;
    }

//display a button on the stock (good, alert, out of stock).
    public function getStock($constant, $products_id, $tag = ' ') {
      $products_stock = '';

      if ($constant == 'number') {
        $products_stock = CLICSHOPPING::getDef('text_stock') . $tag . $this->productsCommon->getProductsStock($products_id);
      } elseif($constant == 'image') {
        $products_stock = $this->productsCommon->getDisplayProductsStock($products_id);
      } else {
        $products_stock = '';
      }

      return $products_stock;
    }

//Flash discount
    public function getFlashDiscount($products_id, $tag = '<br />') {
      $products_flash_discount = '';

      if (!empty($this->productsCommon->getProductsFlashDiscount($products_id))) {
        $products_flash_discount =  CLICSHOPPING::getDef('text_flash_discount') . $tag . $this->productsCommon->getProductsFlashDiscount($products_id);

        return $products_flash_discount;
      }
    }

// Minimum quantity to take an order
    public function getMinOrderQuantityProductDisplay($products_id, $tag = ' ') {
      if ($this->productsCommon->getProductsMinimumQuantityToTakeAnOrder($products_id)  > 1 ) {
        $min_order_quantity_products_display =  CLICSHOPPING::getDef('min_qty_product') . $tag . $this->productsCommon->getProductsMinimumQuantityToTakeAnOrder($products_id);

        return $min_order_quantity_products_display;
      }
    }

// display a message in public function the customer group applied - before submit button
    public function getButtonView($products_id) {
      if ($this->productsCommon->getProductsMinimumQuantity($products_id) != 0 && $this->productsCommon->getProductsQuantity($products_id) != 0) {
        $submit_button_view = $this->productsCommon->getProductsAllowingTakeAnOrderMessage();
        return $submit_button_view;
      }
    }

// Display an input allowing for the customer to insert a quantity
    public function getDisplayInputQuantity($constant, $products_id, $tag = ' ') {
      $input_quantity = '';

      if ($constant == 'False') {
        $input_quantity = '';

        if ($this->productsCommon->getProductsAllowingToInsertQuantity($products_id)) {
          if ($this->productsCommon->getHasProductAttributes($products_id) === false) {
            $input_quantity = CLICSHOPPING::getDef('text_customer_quantity')  . $tag . $this->productsCommon->getProductsAllowingToInsertQuantity($products_id);
          }
        }
      }

      return $input_quantity;
    }

    public function getProductQuantityUnitType($products_id, $tag = ' ') {
      $products_quantity_unit = '';

      if ($this->customer->getCustomersGroupID() == 0) {
        if (!empty( $this->productsCommon->getProductQuantityUnitType($products_id))) {
          $products_quantity_unit = CLICSHOPPING::getDef('text_products_quantity_type') . $tag . $this->productsCommon->getProductQuantityUnitType($products_id);
        }
      } else {
        if (!empty( $this->productsCommon->getProductQuantityUnitTypeCustomersGroup($products_id))) {
          $products_quantity_unit = CLICSHOPPING::getDef('text_products_quantity_type') . $tag . $this->productsCommon->getProductQuantityUnitTypeCustomersGroup($products_id);
        }
      }

      return $products_quantity_unit;
    }


    public function getButtonViewDetails($constant, $products_id, $icon = null, $button_color = 'info', $params = null, $button_size = 'sm') {
      $button = '';

      if ($constant == 'False') {
        if (is_null($icon)) {
          $button = HTML::button(CLICSHOPPING::getDef('button_details'), '', $this->rewriteUrl->getProductNameUrl($products_id), $button_color, $params, $button_size);
        } else {
          $button = HTML::button(null, $icon, $this->rewriteUrl->getProductNameUrl($products_id), $button_color, $params, $button_size);
        }
      }

      return $button;
    }


    public function getImage($constant, $products_id, $parameters = '', $responsive = true, $css = '') {
      $products_image = '';

      if ($constant == 'Medium') {
        if ($this->productsCommon->getProductsImageMedium($products_id) !== false ) {
          $products_image = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::image($this->template->getDirectoryTemplateImages() . $this->productsCommon->getProductsImageMedium($products_id), HTML::outputProtected($this->productsCommon->getProductsName($products_id)), (int)MEDIUM_IMAGE_WIDTH, (int)MEDIUM_IMAGE_HEIGHT, $parameters, $responsive, $css));
        } else {
          $products_image = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::image($this->template->getDirectoryTemplateImages() . $this->productsCommon->getProductsImage($products_id), HTML::outputProtected($this->productsCommon->getProductsName($products_id)), (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT, $parameters, $responsive, $css));
        }
      } else {
        $products_image = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::image($this->template->getDirectoryTemplateImages() . $this->productsCommon->getProductsImage($products_id), HTML::outputProtected($this->productsCommon->getProductsName($products_id)), (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT, $parameters, $responsive, $css));
      }

      return $products_image;
    }

    public function getTicker($constant, $products_id, $cssSpecial, $cssFavorites, $cssFeatured, $cssProductsNew) {
      $ticker = '';

      if ($this->productsCommon->getProductsTickerSpecials() == 'True' && $constant == 'True') {
        $ticker = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_specials'), $cssSpecial, $this->productsCommon->getProductsTickerSpecials($products_id)));
      } elseif ($this->productsCommon->getProductsTickerFavorites() == 'True' && $constant == 'True') {
        $ticker = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_favorite'), $cssFavorites, $this->productsCommon->getProductsTickerFavorites($products_id)));
      } elseif ($this->productsCommon->getProductsTickerFeatured() == 'True' && $constant == 'True') {
        $ticker = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_featured'), $cssFeatured, $this->productsCommon->getProductsTickerFeatured($products_id)));
      } elseif ($this->productsCommon->getProductsTickerProductsNew() == 'True' && $constant == 'True') {
        $ticker = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::tickerImage(CLICSHOPPING::getDef('text_ticker_products_new'), $cssProductsNew, $this->productsCommon->getProductsTickerProductsNew($products_id)));
      }

      return $ticker;
    }

    public function getTickerPourcentage($constant, $products_id, $cssPourcentage) {
      $ticker = '';

      if ($constant == 'True' && !is_null($this->productsCommon->getProductsTickerSpecialsPourcentage($products_id))) {
        $ticker = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::tickerImage($this->productsCommon->getProductsTickerSpecialsPourcentage($products_id), $cssPourcentage, true));
      } else {
        $ticker = '';
      }

      return $ticker;
    }


    public function getProductsModel($products_id, $tag = ' ') {
      if (!empty($this->productsCommon->getProductsModel($products_id))) {
        $products_model = $tag . $this->productsCommon->getProductsModel($products_id);

        return $products_model;
      }
    }


    public function getProductsManufacturer($products_id, $tag = ' ') {
      if (!empty($this->productsCommon->getProductsManufacturer($products_id))) {
        $products_manufacturers =  CLICSHOPPING::getDef('text_manufacturer')  . $tag . $this->productsCommon->getProductsManufacturer();

        return $products_manufacturers;
      }
    }


    public function getProductsPriceByWeight($products_id, $tag = ' ') {
      if (!empty($this->productsCommon->getProductsPriceByWeight($products_id))) {
        $product_price_kilo = CLICSHOPPING::getDef('text_products_price_kilo') . $tag . $this->productsCommon->getProductsPriceByWeight($products_id);

        return $product_price_kilo;
      }
    }

    public function getProductsDateAvailable($products_id, $tag = ' ') {
      if (!empty($this->productsCommon->getProductsDateAvailable($products_id))) {
        $products_date_available =  $this->productsCommon->getProductsDateAvailable($products_id);
        if ($products_date_available > date('Y-m-d H:i:s')) {
          $products_date_available = CLICSHOPPING::getDef('text_date_available') . $tag . DateTime::toShort($products_date_available);
        }

        return $products_date_available;
      }
    }

    public function getProductsOnlyTheShop($products_id) {
      if ($this->productsCommon->getProductsOnlyTheShop($products_id) == 1) {
        $products_only_shop =  CLICSHOPPING::getDef('text_only_shop');

        return $products_only_shop;
      }
    }

    public function getProductsOnlyOnTheWebSite($products_id) {
      if ($this->productsCommon->getProductsOnlyOnTheWebSite($products_id) == 1) {
        $products_only_web =  CLICSHOPPING::getDef('text_only_web');

        return $products_only_web;
      }
    }

    public function getProductsPackaging($products_id, $tag = ' ') {
      if ($this->productsCommon->getProductsPackaging($products_id) == 1) {
        $products_packaging = $this->productsCommon->getProductsPackaging($products_id);
        if ($products_packaging == 1) $products_packaging = CLICSHOPPING::getDef('text_products_info_packaging_text') . $tag . CLICSHOPPING::getDef('text_products_packaging_new');
        if ($products_packaging == 2) $products_packaging = CLICSHOPPING::getDef('text_products_info_packaging_text') . $tag . CLICSHOPPING::getDef('text_products_packaging_repackaged');
        if ($products_packaging == 3) $products_packaging = CLICSHOPPING::getDef('text_products_info_packaging_text') . $tag . CLICSHOPPING::getDef('text_products_packaging_used');

        return $products_packaging;
      }
    }

    public function getProductsShippingDelay($products_id, $tag = ' ') {
      if (!empty($this->productsCommon->getProductsShippingDelay($products_id))) {
        $products_shipping_delay = CLICSHOPPING::getDef('text_display_shipping_delay') . $tag . $this->productsCommon->getProductsShippingDelay($products_id);

        return $products_shipping_delay;
      }
    }


    public function getProductsHeadTag($products_id) {
      if (!empty($this->productsCommon->getProductsHeadTag($products_id))) {
        $products_tag = $this->productsCommon->getProductsHeadTag($products_id);
        $delimiter =',';
        $products_tag = trim(preg_replace('|\\s*(?:' . preg_quote($delimiter) . ')\\s*|', $delimiter, $products_tag));
        $tag = explode(",", $products_tag);

        return  $tag;
      }
    }


    public function getProductsVolume($products_id, $tag = ' ') {
      if (!empty($this->productsCommon->getProductsVolume($products_id))) {
        $products_volume = CLICSHOPPING::getDef('text_products_volume')  . $tag . $this->productsCommon->getProductsVolume($products_id);

        return $products_volume;
      }
    }

    public function getProductsWeight($products_id, $tag = ' / ') {
      if (!empty($this->productsCommon->getProductsPriceByWeight())) {
        $weight_symbol = $this->productsCommon->getSymbolbyProducts($this->productsCommon->getWeightClassIdByProducts($products_id));
        $products_weight = CLICSHOPPING::getDef('text_products_info_price_by_weight') . ' ' .  $this->productsCommon->getProductsPriceByWeight() . $tag . $weight_symbol;

        return $products_weight;
      }
    }

    public function getManufacturerName($products_id) {
      $name= '';

      if (isset($_GET['manufacturers_id']) && is_numeric($_GET['manufacturers_id'])) {
        $manufacturer_id = HTML::sanitize($_GET['manufacturers_id']);
      } else {
        $manufacturer_id = HTML::sanitize($_GET['manufacturers_id']);
      }

      if (isset( $manufacturer_id) && !is_null( $manufacturer_id)) {
        $name = HTML::link(CLICSHOPPING::link(null, 'Products&Description&manufacturers_id=' .  $manufacturer_id . '&products_id=' . $products_id), '<span itemprop="name">' .   $this->productsCommon->getProductsName($products_id) . '</span>', 'itemprop="url"');
      } else {
        $name = HTML::link(CLICSHOPPING::link(null, 'Products&Description&' . ($this->category->getPath() ? 'cPath=' . $this->category->getPath() . '&' : '') . 'products_id=' . $products_id), '<span itemprop="name">' .   $this->productsCommon->getProductsName($products_id) . '</span>', 'itemprop="url"');
      }

      return $name;
    }

    public function getManufacturerImage($products_id, $products_image) {
      $image = '';

      if (isset($_GET['manufacturers_id']) && is_numeric($_GET['manufacturers_id'])) {
        $manufacturer_id = HTML::sanitize($_GET['manufacturers_id']);
        $image = HTML::link(CLICSHOPPING::link(null, 'Products&Description&manufacturers_id=' . $manufacturer_id . '&products_id=' . $products_id), HTML::image($this->template->getDirectoryTemplateImages() . $products_image, HTML::outputProtected($this->productsCommon->getProductsName($products_id), (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT, null, true)));
      } else {
        $image = HTML::link($this->rewriteUrl->getProductNameUrl($products_id, 'products_id=', ($this->category->getPath() ? 'cPath=' . $this->category->getPath() . '&' : '') . 'products_id=' . $products_id), HTML::image($this->template->getDirectoryTemplateImages() . $products_image, HTML::outputProtected($this->productsCommon->getProductsName($products_id)), (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT, null, true));
      }

      return $image;
    }
  }