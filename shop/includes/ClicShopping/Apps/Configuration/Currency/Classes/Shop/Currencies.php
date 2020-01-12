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

  namespace ClicShopping\Apps\Configuration\Currency\Classes\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  use ClicShopping\Sites\Shop\Tax;

  class Currencies
  {

    public $currencies = [];
    protected $db;

    public function __construct()
    {
      $this->db = Registry::get('Db');
      $this->currencies = [];

      $Qcurrencies = $this->db->prepare('select currencies_id as id,
                                                code,
                                                title,
                                                symbol_left,
                                                symbol_right,
                                                decimal_point,
                                                thousands_point,
                                                decimal_places,
                                                value,
                                                surcharge
                                         from :table_currencies
                                         where status = 1
                                        ');

      $Qcurrencies->execute();
      $Qcurrencies->setCache('currencies');

      $currencies = $Qcurrencies->fetchAll();

      foreach ($currencies as $c) {
        $this->currencies[$c['code']] = [
          'id' => (int)$c['id'],
          'title' => $c['title'],
          'symbol_left' => $c['symbol_left'],
          'symbol_right' => $c['symbol_right'],
          'decimal_point' => $c['decimal_point'],
          'thousands_point' => $c['thousands_point'],
          'decimal_places' => (int)$c['decimal_places'],
          'value' => (float)$c['value'],
          'surcharge' => (float)$c['surcharge']
        ];
      }

      if (!isset($this->default) && ((float)$c['value'] === 1.0)) {
        $this->default = $c['code'];
      }
    }

    /**
     * @param $number
     * @param bool $calculate_currency_value
     * @param string $currency_type
     * @param null $currency_value
     * @return string
     */
    public function format(float $number, $calculate_currency_value = true, string $currency_type = '', $currency_value = null) :string
    {
      if (empty($currency_type) && CLICSHOPPING::getSite() == 'Shop') {
        $currency_type = $_SESSION['currency'];
      }

      if (CLICSHOPPING::getSite() == 'ClicShoppingAdmin') {
        $currency_type = DEFAULT_CURRENCY;
      }

      if ($calculate_currency_value === true) {
        $rate = (!is_null($currency_value)) ? $currency_value : $this->currencies[$currency_type]['value'];

        if ($this->currencies[$currency_type]['surcharge'] > 0) {
          $rate += ($rate * $this->currencies[$currency_type]['surcharge']);
        }

        $format_string = '&nbsp;' . $this->currencies[$currency_type]['symbol_left'] . number_format(round($number * $rate, $this->currencies[$currency_type]['decimal_places']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . '&nbsp;' . $this->currencies[$currency_type]['symbol_right'];
      } else {
        $format_string = '&&nbsp;' . $this->currencies[$currency_type]['symbol_left'] . number_format(round($number, $this->currencies[$currency_type]['decimal_places']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . '&nbsp;' . $this->currencies[$currency_type]['symbol_right'];
      }

      return $format_string;
    }

    /**
     * @param $products_price
     * @param $products_tax
     * @param int $quantity
     * @return float|int
     */
    public function calculatePrice(float $products_price, $products_tax, int $quantity = 1) :float
    {
      return round(Tax::addTax($products_price, $products_tax), $this->currencies[$_SESSION['currency']]['decimal_places']) * $quantity;
    }

    /**
     * @param $code
     * @return bool
     */
    public function isSet(string $code) :bool
    {
      if (isset($this->currencies[$code]) && !is_null($this->currencies[$code])) {
        return true;
      } else {
        return false;
      }
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getValue(string $code): float
    {
      return $this->currencies[$code]['value'];
    }

    /**
     * @param $code
     * @return mixed
     */
    public function getDecimalPlaces(string $code) :string
    {
      return $this->currencies[$code]['decimal_places'];
    }

    /**
     * add a tag like HT (whithout taxes) or TTC (with taxes) example
     * @return mixed
     */
    private function priceTag() :string
    {
      $CLICSHOPPING_Tax = Registry::get('Tax');

      $tag = $CLICSHOPPING_Tax->getTag();

      if (isset($tag)) {
        $pricetag = $tag;
      } else {
        $pricetag = $tag;
      }

      return $pricetag;
    }

    /**
     * Add a tag after the price ex 100 euros HT or TTC
     * @param $products_price
     * @param $products_tax
     * @param int $quantity
     * @return string
     */
    public function displayPrice(float $products_price, $products_tax, int $quantity = 1)
    {
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

      if ((($CLICSHOPPING_Customer->getCustomersGroupID() == 0) && (DISPLAY_PRODUCT_PRICE_VALUE_TAX == 'true')) || (($CLICSHOPPING_Customer->getCustomersGroupID() != 0) && (DISPLAY_PRODUCT_PRICE_VALUE_TAX_PRO == 'true'))) {
        return $this->format($this->calculatePrice($products_price, $products_tax, $quantity)) . ' ' . $this->priceTag();
      } else {

// do not display if the price 0
        if (NOT_DISPLAY_PRICE_ZERO == 'false') {
          if ($products_price > 0) {
            return $this->format($this->calculatePrice($products_price, $products_tax, $quantity));
          } else {
            return '';
          }
        } else {
          return $this->format($this->calculatePrice($products_price, $products_tax, $quantity));
        } // END NOT_DISPLAY_PRICE_ZERO
      }
    }

    /**
     * Product Price per kilo calculation
     * @param $products_price
     * @param $products_weight
     * @param $value
     * @param $products_tax
     * @param int $quantity
     * @return bool|string
     */
    public function displayPriceKilo(float $products_price, float $products_weight, float $value, float $products_tax, int $quantity = 1)
    {
      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (($products_weight > 0) && ($products_weight > '0') && ($value == 1)) {
        $products_price_kilo = round(($products_price / $products_weight), 2);

        if ((($CLICSHOPPING_Customer->getCustomersGroupID() == 0) && (DISPLAY_PRODUCT_PRICE_VALUE_TAX == 'true')) || (($CLICSHOPPING_Customer->getCustomersGroupID() != 0) && (DISPLAY_PRODUCT_PRICE_VALUE_TAX_PRO == 'true'))) {
          return $this->format($this->calculatePrice($products_price_kilo, $products_tax, $quantity)) . ' ' . $this->priceTag() . '';
        } else {
          return $this->format($this->calculatePrice($products_price_kilo, $products_tax, $quantity));
        }
      } else {
        return false;
      }
    }

    /**
     * @param string $number
     * @param string|null $currency_code
     * @param bool $use_locale
     * @return string
     */
    public function trim(string $number, string $currency_code = null, bool $use_locale = true): string
    {
      if (!isset($currency_code)) {
        $currency_code = $this->getDefault();
      }

      $dec_point = '.';
      if (!empty($this->currencies[$_SESSION['currency']]['thousands_point'])) {
        $dec_point = $this->currencies[$_SESSION['currency']]['thousands_point'];
      }

      $number = str_replace($dec_point . str_repeat('0', $this->currencies[$currency_code]['decimal_places']), '', $number);

      return $number;
    }

    /**
     * @param string|null $key
     * @param string|null $currency_code
     * @return mixed array|string
     */
    public function get(string $key = null, string $currency_code = null)
    {
      if (!isset($currency_code)) {
        $currency_code = $this->getDefault();
      }

      if (isset($key)) {
        return $this->currencies[$currency_code][$key];
      }

      return $this->currencies[$currency_code];
    }

    /**
     * @param int $id
     * @return string|null
     */
    public function getCode(int $id): ?string
    {
      foreach ($this->currencies as $code => $c) {
        if ($c['id'] === $id) {
          return $code;
        }
      }
      
      return null;
    }

    /**
     * Dispaly a Currencies DropDown
     * @param string $class
     * @return string
     */
    public function getCurrenciesDropDown($class = '')
    {
      if ((count($this->currencies) > 1)) {
        reset($this->currencies);
        $currency_header = '';

        $currencies_array = $this->getAll();

        $hidden_get_variables = '';

        foreach ($_GET as $key => $value) {
          if (is_string($value) && ($key != 'currency') && ($key != session_name()) && ($key != 'x') && ($key != 'y')) {
            $hidden_get_variables .= HTML::hiddenField($key, $value);
          }
        }

        if (!isset($_GET['Checkout'])) {
          $currency_header .= HTML::form('currencies', CLICSHOPPING::link(), 'get', null, ['session_id' => true]);
          $currency_header .= '<label for="CurrencyDropDown" class="sr-only">Currency</label>';
          $currency_header .= HTML::selectField('currency', $currencies_array, HTML::sanitize($_SESSION['currency']), 'id="CurrencyDropDown" class="' . $class . '" onchange="this.form.submit();"') . $hidden_get_variables;
          $currency_header .= '</form>';
        } else {
          $currency_header = '';
        }

        return $currency_header;
      }
    }


    /**
     * @return array
     */
    public function getAll(): array
    {
      $result = [];

      foreach ($this->currencies as $code => $c) {
        $result[] = [
          'id' => $code,
          'text' => $c['title']
        ];
      }

      return $result;
    }

    /**
     * @param float $number
     * @param bool $use_trim
     * @return array
     */
    public function showAll(float $number, bool $use_trim = false): array
    {
      $result = [];

      foreach (array_keys($this->currencies) as $code) {
        $value = $this->show($number, $code);

        if ($use_trim === true) {
          $value = $this->trim($value);
        }

        $result[$code] = $value;
      }

      return $result;
    }

    /**
     * @param bool $true_default
     * @return string|null
     */
    public function getDefault(bool $true_default = false): ?string
    {
      return (($true_default === false) && $this->hasSelected()) ? $this->selected : $this->default;
    }

    /**
     * @return string|null
     */
    public function getSelected(): ?string
    {
      return $this->selected;
    }

    /**
     * @return bool
     */
    public function hasSelected(): bool
    {
      return isset($this->selected);
    }

    /**
     * @param string $code
     * @return bool
     */
    public function setSelected(string $code): bool
    {
      if ($this->exists($code)) {
        $this->selected = $code;

        return true;
      }

      return false;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function exists(string $code): bool
    {
      return array_key_exists($code, $this->currencies);
    }
  }
