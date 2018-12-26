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

  namespace ClicShopping\Apps\Configuration\Currency\Classes\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
	use ClicShopping\OM\HTML;

  use ClicShopping\Sites\Shop\Tax;

  class Currencies {

    public $currencies = [];
    protected $db;

    Public function __construct() {
      $this->db = Registry::get('Db');
      $this->currencies = [];

      $Qcurrencies = $this->db->query('select code,
                                              title,
                                              symbol_left,
                                              symbol_right,
                                              decimal_point,
                                              thousands_point,
                                              decimal_places,
                                              value
                                       from :table_currencies
                                      ');

      $Qcurrencies->execute();

      while ($Qcurrencies->fetch()) {
        $this->currencies[$Qcurrencies->value('code')] = ['title' => $Qcurrencies->value('title'),
                                                          'symbol_left' => $Qcurrencies->value('symbol_left'),
                                                          'symbol_right' => $Qcurrencies->value('symbol_right'),
                                                          'decimal_point' => $Qcurrencies->value('decimal_point'),
                                                          'thousands_point' => $Qcurrencies->value('thousands_point'),
                                                          'decimal_places' => $Qcurrencies->valueInt('decimal_places'),
                                                          'value' => $Qcurrencies->valueDecimal('value')
                                                          ];
      }
    }

    public function format($number, $calculate_currency_value = true, $currency_type = '', $currency_value = null) {
      if ( empty($currency_type) && CLICSHOPPING::getSite() == 'Shop' ) {
        $currency_type = $_SESSION['currency'];
      }

     if (CLICSHOPPING::getSite() == 'ClicShoppingAdmin') {
        $currency_type = DEFAULT_CURRENCY;
      }

      if ($calculate_currency_value === true) {
        $rate = (!is_null($currency_value)) ? $currency_value : $this->currencies[$currency_type]['value'];

        $format_string = '&nbsp;' . $this->currencies[$currency_type]['symbol_left'] . number_format(round($number * $rate, $this->currencies[$currency_type]['decimal_places']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . '&nbsp;' . $this->currencies[$currency_type]['symbol_right'];
      } else {
        $format_string = '&&nbsp;' . $this->currencies[$currency_type]['symbol_left'] . number_format(round($number, $this->currencies[$currency_type]['decimal_places']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . '&nbsp;' . $this->currencies[$currency_type]['symbol_right'];
      }

      return $format_string;
    }

    public function calculate_price($products_price, $products_tax, $quantity = 1) {
      return round(Tax::addTax($products_price, $products_tax), $this->currencies[$_SESSION['currency']]['decimal_places']) * $quantity;
    }

    public function is_set($code) {
      if (isset($this->currencies[$code]) && !is_null($this->currencies[$code])) {
        return true;
      } else {
        return false;
      }
    }

    public function get_value($code) {
      return $this->currencies[$code]['value'];
    }

    public function get_decimal_places($code) {
      return $this->currencies[$code]['decimal_places'];
    }

/*
    public function value($code) {
      if ( $this->get_value($code) ) {
        return $this->currencies[$code]['value'][$code]['value'];
      }

      return false;
    }
*/

    public function getData() {
      return $this->currencies;
    }

    private function priceTag() {
      $CLICSHOPPING_Tax = Registry::get('Tax');

      $tag = $CLICSHOPPING_Tax->getTag();

      if (isset($tag)) {
        $pricetag = $tag;
      } else {
        $pricetag = $tag;
      }

      return $pricetag;
    }



// Formatage du prix du produit
// Add a tag after the price ex 100 euros HT or TTC
    public function display_price($products_price, $products_tax, $quantity = 1) {
      $CLICSHOPPING_Customer = Registry::get('Customer');
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');

// Marketing : price is update by discount of the quantity and in function the product
//Display only in shoppingCart
      if (isset($_GET['Cart']) && $_GET['Cart'] == 'Cart') {
        $new_price_with_discount_quantity = $CLICSHOPPING_ProductsCommon->getProductsNewPriceByDiscountByQuantity($_SESSION['ProductsID'], $quantity, $products_price);

        if ($new_price_with_discount_quantity > 0) {
          $products_price = $CLICSHOPPING_ProductsCommon->getProductsNewPriceByDiscountByQuantity($_SESSION['ProductsID'], $quantity, $products_price);
          unset($_SESSION['ProductsID']);
        }
      }

      if ((($CLICSHOPPING_Customer->getCustomersGroupID() == 0) && (DISPLAY_PRODUCT_PRICE_VALUE_TAX == 'true')) || (($CLICSHOPPING_Customer->getCustomersGroupID()!= 0) && (DISPLAY_PRODUCT_PRICE_VALUE_TAX_PRO == 'true')) ) {
        return $this->format($this->calculate_price($products_price, $products_tax, $quantity)) . ' ' . $this->priceTag();
      } else {

// do not display if the price 0
        if (NOT_DISPLAY_PRICE_ZERO == 'false') {
          if ($products_price > 0) {
            return $this->format($this->calculate_price($products_price, $products_tax, $quantity));
          } else {
            return '';
          }
        } else {
          return $this->format($this->calculate_price($products_price, $products_tax, $quantity));
        } // END NOT_DISPLAY_PRICE_ZERO
      }
    }

// Product Price per kilo calculation
// Calcul du prix du produit au kilo
    public function displayPriceKilo($products_price, $products_weight, $value, $products_tax, $quantity = 1) {
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (($products_weight > 0) && ($products_weight > '0') && ($value == 1)) {
        $products_price_kilo = round(($products_price / $products_weight),2);

        if ((($CLICSHOPPING_Customer->getCustomersGroupID() == 0) && (DISPLAY_PRODUCT_PRICE_VALUE_TAX == 'true')) || (($CLICSHOPPING_Customer->getCustomersGroupID() != 0) && (DISPLAY_PRODUCT_PRICE_VALUE_TAX_PRO == 'true'))) {
          return $this->format($this->calculate_price($products_price_kilo, $products_tax, $quantity)) . ' ' . $this->priceTag() . '';
        } else {
          return $this->format($this->calculate_price($products_price_kilo, $products_tax, $quantity));
        }
      } else {
        return false;
      }
    }

/**
	* Dispaly a Currencies DropDown
	* @return string
*/
    public function getCurrenciesDropDown($class = '') {
    $CLICSHOPPING_Currencies = Registry::get('Currencies');

      if (isset($CLICSHOPPING_Currencies) && is_object($CLICSHOPPING_Currencies) && (count($CLICSHOPPING_Currencies->currencies) > 1)) {
        reset($CLICSHOPPING_Currencies->currencies);
        $currencies_array = [];

        foreach($CLICSHOPPING_Currencies->currencies as $key => $value) {
          $currencies_array[] = ['id' => $key,
                                 'text' => $value['title']
                                ];
        }

        $hidden_get_variables = '';

        foreach ( $_GET as $key => $value ) {
          if ( is_string($value) && ($key != 'currency') && ($key != session_name()) && ($key != 'x') && ($key != 'y') ) {
            $hidden_get_variables .= HTML::hiddenField($key, $value);
          }
        }

        $currency_header = HTML::form('currencies', CLICSHOPPING::link(), 'get', null, ['session_id' => true]);
        $currency_header .= '<label for="CurrencyDropDown" class="sr-only">Currency</label>';
        $currency_header .= HTML::selectField('currency', $currencies_array, $_SESSION['currency'], 'id="CurrencyDropDown" class="' . $class . '" onchange="this.form.submit();"') . $hidden_get_variables;
        $currency_header .= '</form>';
      }

      return $currency_header;
    }
  }
