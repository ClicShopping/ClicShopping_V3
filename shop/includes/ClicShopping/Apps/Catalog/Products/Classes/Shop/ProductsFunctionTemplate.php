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
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTTP;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\DateTime;

  use ClicShopping\Sites\Common\HTMLOverrideCommon;

  class ProductsFunctionTemplate
  {

    protected $productsCommon;
    protected $customer;
    protected $template;
    protected $category;
    protected $rewriteUrl;

    public function __construct()
    {
      $this->productsCommon = Registry::get('ProductsCommon');
      $this->customer = Registry::get('Customer');
      $this->template = Registry::get('Template');
      $this->category = Registry::get('Category');
      $this->rewriteUrl = Registry::get('RewriteUrl');
    }

    /**
     * product url
     */
    public function getProductsUrlRewrited()
    {
      return $this->rewriteUrl;
    }

    /**
     * @param $products_id
     * @return string
     */
    public function getProductsNameUrl($products_id) :string
    {
      $products_name = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), '<span itemprop="name">' . $this->productsCommon->getProductsName($products_id) . '</span>', 'itemprop="url"');

      return $products_name;
    }

//display a button on the stock (good, alert, out of stock).

    /**
     * @param string $constant
     * @param int $products_id
     * @param string $tag
     * @return string
     */
    public function getStock(string $constant, $products_id, string $tag = ' '): string
    {
      if ($constant == 'number') {
        $products_stock = CLICSHOPPING::getDef('text_stock') . $tag . $this->productsCommon->getProductsStock($products_id);
      } elseif ($constant == 'image') {
        $products_stock = $this->productsCommon->getDisplayProductsStock($products_id);
      } else {
        $products_stock = '';
      }

      return $products_stock;
    }

    /**
     * @param int $products_id
     * @param string $tag
     * @return string
     */
    public function getFlashDiscount(int $products_id, string $tag = '<br />') :string
    {
      $products_flash_discount = '';

      if (!empty($this->productsCommon->getProductsFlashDiscount($products_id))) {
        $products_flash_discount = CLICSHOPPING::getDef('text_flash_discount') . $tag . $this->productsCommon->getProductsFlashDiscount($products_id);
      }

      return $products_flash_discount;
    }

    /**
     * Minimum quantity to take an order
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getMinOrderQuantityProductDisplay($products_id, string $tag = ' ') :string
    {
      if ($this->productsCommon->getProductsMinimumQuantityToTakeAnOrder($products_id) > 1) {
        $min_order_quantity_products_display = CLICSHOPPING::getDef('min_qty_product') . $tag . $this->productsCommon->getProductsMinimumQuantityToTakeAnOrder($products_id);

        return $min_order_quantity_products_display;
      } else {
        return '';
      }
    }

// display a message in public function the customer group applied - before submit button
    public function getButtonView($products_id)
    {
      if ($this->productsCommon->getProductsMinimumQuantity($products_id) != 0 && $this->productsCommon->getProductsQuantity($products_id) != 0) {
        $submit_button_view = $this->productsCommon->getProductsAllowingTakeAnOrderMessage();
        return $submit_button_view;
      }
    }

    /**
     * Display an input allowing for the customer to insert a quantity
     * @param string $constant
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getDisplayInputQuantity(string $constant, $products_id, string $tag = ' ') :string
    {
      $input_quantity = '';

      if ($constant == 'False') {
        $input_quantity = '';

        if ($this->productsCommon->getProductsAllowingToInsertQuantity($products_id)) {
          if ($this->productsCommon->getHasProductAttributes($products_id) === false) {
            $input_quantity = CLICSHOPPING::getDef('text_customer_quantity') . $tag . $this->productsCommon->getProductsAllowingToInsertQuantity($products_id);
          }
        }
      }

      return $input_quantity;
    }

    /**
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getProductQuantityUnitType($products_id, string $tag = ' ') :string
    {
      $products_quantity_unit = '';

      if ($this->customer->getCustomersGroupID() == 0) {
        if (!empty($this->productsCommon->getProductQuantityUnitType($products_id))) {
          $products_quantity_unit = CLICSHOPPING::getDef('text_products_quantity_type') . $tag . $this->productsCommon->getProductQuantityUnitType($products_id);
        }
      } else {
        if (!empty($this->productsCommon->getProductQuantityUnitTypeCustomersGroup($products_id))) {
          $products_quantity_unit = CLICSHOPPING::getDef('text_products_quantity_type') . $tag . $this->productsCommon->getProductQuantityUnitTypeCustomersGroup($products_id);
        }
      }

      return $products_quantity_unit;
    }

    /**
     * @param string $constant
     * @param $products_id
     * @param string|null $icon
     * @param string $button_color
     * @param null $params
     * @param string $button_size
     * @return string
     */
    public function getButtonViewDetails(string $constant, $products_id, ?string $icon = null, string $button_color = 'info', $params = null, string $button_size = 'sm') :string
    {
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

    /**
     * @param string $constant
     * @param $products_id
     * @param string $parameters
     * @param bool $responsive
     * @param string $css
     * @return string
     */
    public function getImage(string $constant, $products_id, $parameters = '', bool $responsive = true, string $css = '') :string
    {
      if ($constant == 'Medium') {
        if ($this->productsCommon->getProductsImageMedium($products_id) !== false) {
          $products_image = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::image($this->template->getDirectoryTemplateImages() . $this->productsCommon->getProductsImageMedium($products_id), HTML::outputProtected($this->productsCommon->getProductsName($products_id)), (int)MEDIUM_IMAGE_WIDTH, (int)MEDIUM_IMAGE_HEIGHT, $parameters, $responsive, $css));
        } else {
          $products_image = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::image($this->template->getDirectoryTemplateImages() . $this->productsCommon->getProductsImage($products_id), HTML::outputProtected($this->productsCommon->getProductsName($products_id)), (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT, $parameters, $responsive, $css));
        }
      } else {
        $products_image = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::image($this->template->getDirectoryTemplateImages() . $this->productsCommon->getProductsImage($products_id), HTML::outputProtected($this->productsCommon->getProductsName($products_id)), (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT, $parameters, $responsive, $css));
      }

      return $products_image;
    }

    /**
     * @param string $constant
     * @param $products_id
     * @param string $cssSpecial
     * @param string $cssFavorites
     * @param string $cssFeatured
     * @param string $cssProductsNew
     * @return string
     */
    public function getTicker(string $constant, $products_id, string $cssSpecial, string $cssFavorites, string $cssFeatured, string $cssProductsNew): string
    {
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

    /**
     * @param string $constant
     * @param $products_id
     * @param string $cssPourcentage
     * @return string
     */
    public function getTickerPourcentage(string $constant, $products_id, string $cssPourcentage) :string
    {
      if ($constant == 'True' && !is_null($this->productsCommon->getProductsTickerSpecialsPourcentage($products_id))) {
        $ticker = HTML::link($this->rewriteUrl->getProductNameUrl($products_id), HTML::tickerImage($this->productsCommon->getProductsTickerSpecialsPourcentage($products_id), $cssPourcentage, true));
      } else {
        $ticker = '';
      }

      return $ticker;
    }

    /**
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getProductsModel($products_id, string $tag = ' '): string
    {
      if (!empty($this->productsCommon->getProductsModel($products_id))) {
        $products_model = $tag . $this->productsCommon->getProductsModel($products_id);

        return $products_model;
      } else {
        return '';
      }
    }

    /**
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getProductsManufacturer($products_id, string $tag = ' ') :string
    {
      if (!empty($this->productsCommon->getProductsManufacturer($products_id))) {
        $products_manufacturers = CLICSHOPPING::getDef('text_manufacturer') . $tag . $this->productsCommon->getProductsManufacturer($products_id);

        return $products_manufacturers;
      } else {
        return '';
      }
    }

    /**
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getProductsPriceByWeight($products_id, string $tag = ' ') :string
    {
      if (!empty($this->productsCommon->getProductsPriceByWeight($products_id))) {
        $product_price_kilo = CLICSHOPPING::getDef('text_products_price_kilo') . $tag . $this->productsCommon->getProductsPriceByWeight($products_id);

        return $product_price_kilo;
      } else {
        return '';
      }
    }

    /**
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getProductsDateAvailable($products_id, string $tag = ' ') :string
    {
      if (!empty($this->productsCommon->getProductsDateAvailable($products_id))) {
        $products_date_available = $this->productsCommon->getProductsDateAvailable($products_id);
        if ($products_date_available > date('Y-m-d H:i:s')) {
          $products_date_available = CLICSHOPPING::getDef('text_date_available') . $tag . DateTime::toShort($products_date_available);
        }

        return $products_date_available;
      } else {
        return '';
      }
    }

    /*
     *
     */
    public function getProductsOnlyTheShop($products_id): string
    {
      if ($this->productsCommon->getProductsOnlyTheShop($products_id) == 1) {
        $products_only_shop = CLICSHOPPING::getDef('text_only_shop');

        return $products_only_shop;
      } else {
        return '';
      }
    }

    /**
     * @param $products_id
     * @return string
     */
    public function getProductsOnlyOnTheWebSite($products_id) :string
    {
      if ($this->productsCommon->getProductsOnlyOnTheWebSite($products_id) == 1) {
        $products_only_web = CLICSHOPPING::getDef('text_only_web');

        return $products_only_web;
      } else {
        return '';
      }
    }

    /**
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getProductsPackaging($products_id, string $tag = ' ') :string
    {
      $products_packaging = $this->productsCommon->getProductsPackaging($products_id);

      if ($products_packaging == 0) $products_packaging = '';
      if ($products_packaging == 1) $products_packaging = CLICSHOPPING::getDef('text_products_info_packaging_text') . $tag . CLICSHOPPING::getDef('text_products_packaging_new');
      if ($products_packaging == 2) $products_packaging = CLICSHOPPING::getDef('text_products_info_packaging_text') . $tag . CLICSHOPPING::getDef('text_products_packaging_repackaged');
      if ($products_packaging == 3) $products_packaging = CLICSHOPPING::getDef('text_products_info_packaging_text') . $tag . CLICSHOPPING::getDef('text_products_packaging_used');

      return $products_packaging;
    }

    /**
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getProductsShippingDelay($products_id, string $tag = ' ') :string
    {
      if (!empty($this->productsCommon->getProductsShippingDelay($products_id))) {
        $products_shipping_delay = CLICSHOPPING::getDef('text_display_shipping_delay') . $tag . $this->productsCommon->getProductsShippingDelay($products_id);

        return $products_shipping_delay;
      } else {
        return '';
      }
    }

    /**
     * @param $products_id
     * @return string
     */
    public function getProductsHeadTag($products_id) :string
    {
      if (!empty($this->productsCommon->getProductsHeadTag($products_id))) {
        $products_tag = $this->productsCommon->getProductsHeadTag($products_id);
        $delimiter = ',';
        $products_tag = trim(preg_replace('|\\s*(?:' . preg_quote($delimiter, null) . ')\\s*|', $delimiter, $products_tag));
        $tag = explode(',', $products_tag);

        return $tag;
      } else {
        return '';
      }
    }

    /**
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getProductsVolume($products_id, string $tag = ' ') :string
    {
      if (!empty($this->productsCommon->getProductsVolume($products_id))) {
        $products_volume = CLICSHOPPING::getDef('text_products_volume') . $tag . $this->productsCommon->getProductsVolume($products_id);

        return $products_volume;
      } else {
        return '';
      }
    }

    /**
     * @param $products_id
     * @param string $tag
     * @return string
     */
    public function getProductsWeight($products_id, string $tag = ' / ') :string
    {
      if (!empty($this->productsCommon->getProductsWeight($products_id))) {
        $weight_symbol = $this->productsCommon->getSymbolbyProducts($this->productsCommon->getWeightClassIdByProducts($products_id));
        $products_weight = CLICSHOPPING::getDef('text_products_weight') . '  ' . $this->productsCommon->getProductsWeight($products_id) . $tag . $weight_symbol;

        return $products_weight;
      } else {
        return '';
      }
    }

    /**
     * @param $products_id
     * @return string
     */
    public function getManufacturerName($products_id) :string
    {
      if (isset($_GET['manufacturersId']) && !is_null($_GET['manufacturersId']) && is_numeric($_GET['manufacturersId'])) {
        $manufacturer_id = HTML::sanitize($_GET['manufacturersId']);
        $name = HTML::link(CLICSHOPPING::link(null, 'Products&Description&manufacturersId=' . $manufacturer_id . '&products_id=' . $products_id), '<span itemprop="name">' . $this->productsCommon->getProductsName($products_id) . '</span>', 'itemprop="url"');
      } else {
        $name = HTML::link(CLICSHOPPING::link(null, 'Products&Description&' . ($this->category->getPath() ? 'cPath=' . $this->category->getPath() . '&' : '') . 'products_id=' . $products_id), '<span itemprop="name">' . $this->productsCommon->getProductsName($products_id) . '</span>', 'itemprop="url"');
      }

      return $name;
    }

    /**
     * @param $products_id
     * @param string $products_image
     * @return string
     */
    public function getManufacturerImage($products_id, string $products_image) :string
    {
      if (isset($_GET['manufacturersId']) && is_numeric($_GET['manufacturersId'])) {
        $manufacturer_id = HTML::sanitize($_GET['manufacturersId']);
        $image = HTML::link(CLICSHOPPING::link(null, 'Products&Description&manufacturersId=' . $manufacturer_id . '&products_id=' . $products_id), HTML::image($this->template->getDirectoryTemplateImages() . $products_image, HTML::outputProtected($this->productsCommon->getProductsName($products_id), (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT, null, true)));
      } else {
        $image = HTML::link($this->rewriteUrl->getProductNameUrl($products_id, 'products_id=', ($this->category->getPath() ? 'cPath=' . $this->category->getPath() . '&' : '') . 'products_id=' . $products_id), HTML::image($this->template->getDirectoryTemplateImages() . $products_image, HTML::outputProtected($this->productsCommon->getProductsName($products_id)), (int)SMALL_IMAGE_WIDTH, (int)SMALL_IMAGE_HEIGHT, null, true));
      }

      return $image;
    }

    /**
     * @param $products_id
     * @return string
     */
    public function getProductslength($products_id) :string
    {
      if (!empty($this->productsCommon->getProductsDimension($products_id))) {
        $products_length = CLICSHOPPING::getDef('text_products_length') . ' : ' . $this->productsCommon->getProductsDimension($products_id);

        return $products_length;
      } else {
        return '';
      }
    }

    /**
     * @param $products_id
     * @return string
     */
    public function getProductJsonLd($products_id): string
    {
      $CLICSHOPPING_Reviews = Registry::get('Reviews');

      if ($this->productsCommon->getProductsStock($products_id) > 0) {
        $stock = 'InStock';
      } else {
        $stock = 'OutofStock';
      }

      if (STOCK_ALLOW_CHECKOUT == 'true') {
        $stock = 'InStock';
      }

      $products_packaging = $this->productsCommon->getProductsPackaging($products_id);

      if ($products_packaging == 0) $products_packaging = 'http://schema.org/NewCondition'; // default newCondition
      if ($products_packaging == 1) $products_packaging = 'http://schema.org/NewCondition';
      if ($products_packaging == 2) $products_packaging = 'http://schema.org/RefurbishedCondition';
      if ($products_packaging == 3) $products_packaging = 'http://schema.org/UsedCondition';

      $price = $this->productsCommon->getDisplayPriceGroupWithoutCurrencies($products_id);

      if ($CLICSHOPPING_Reviews->getAverageProductReviews($products_id) == 0) {
        $review_average = 1;
      } else {
        $review_average = $CLICSHOPPING_Reviews->getAverageProductReviews($products_id);
      }

//description
      $str = $this->productsCommon->getProductsDescription($products_id);
      $str = str_replace('"', '', $str);
      $str = HTMLOverrideCommon::stripHtmlTags($str);
      $description = HTMLOverrideCommon::cleanHtml($str);

      $name = str_replace('"', '', $this->productsCommon->getProductsName($products_id));
      $name = HTMLOverrideCommon::cleanHtml($name);

      $output = '
      <script defer type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "Product",
  "name": "' .  $name . '",
  "model": "' .  $this->productsCommon->getProductsModel($products_id) . '",
  "image": [
    "' . HTTP::typeUrlDomain() . $this->template->getDirectoryTemplateImages() . $this->productsCommon->getProductsImage($products_id) . '",
    "' . HTTP::typeUrlDomain() . $this->template->getDirectoryTemplateImages() . $this->productsCommon->getProductsImageMedium($products_id) . '"
   ],
  "description": "' . $description . '",
  "sku": "' . $this->productsCommon->getProductsSKU($products_id) . '",
  "mpn": "' . $this->productsCommon->getProductsSKU($products_id) . '", 
  "brand": {
    "@type": "Thing",
    "name": "' . $this->productsCommon->getProductsManufacturer($products_id) . '"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "' . $review_average . '",
    "reviewCount": "' . $CLICSHOPPING_Reviews->getCount($products_id) .'"
  },
  "offers": {
    "@type": "Offer",
    "url": "' . $this->getProductsUrlRewrited()->getProductNameUrl($products_id) . '",
    "priceCurrency": "' . HTML::output(HTML::sanitize($_SESSION['currency'])) . '",
    "price": "' . $price . '",
    "priceValidUntil": "",
    "itemCondition": "https://schema.org/' .$products_packaging . '",
    "availability": "https://schema.org/' . $stock . '",
    "seller": {
      "@type": "Organization",
      "name": "' . HTML::output(STORE_NAME) .'"
    }
  }
}
</script>      
      ';
      
      return $output;
    }
  }